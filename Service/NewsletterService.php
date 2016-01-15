<?php

namespace Necktie\NewsletterBundle\Service;

use Doctrine\Common\Cache\RedisCache;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Necktie\AppBundle\Entity\User;
use Necktie\NewsletterBundle\Entity\Newsletter;
use Necktie\NewsletterBundle\Exception\NewsletterException;
use Necktie\NewsletterBundle\NewsletterDriverInterface;
use Predis\ClientInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class NewsletterInterfaceService.
 */
class NewsletterService implements NewsletterInterface
{

    /** @var NewsletterDriverInterface[] */
    private $drivers = [];

    /** @var  EventDispatcher */
    private $ev;

    /** @var  FlashBag */
    private $flashBag;

    /** @var  EntityManager */
    private $em;

    /** @var  Session */
    private $session;

    /** @var  Logger */
    private $logger;

    /** @var  array */
    private $lists;

    /** @var RedisCache */
    private $cache;


    /**
     * @param EventDispatcher $ev
     * @param FlashBag $flashBag
     * @param EntityManager $em
     * @param Session $session
     * @param Logger $logger
     * @param ClientInterface $cache
     */
    public function __construct($ev, $flashBag, $em, $session, $logger, ClientInterface $cache)
    {
        $this->ev = $ev;
        $this->flashBag = $flashBag;
        $this->em = $em;
        $this->session = $session;
        $this->logger = $logger;
        $this->cache = $cache;
    }


    /**
     * @inheritdoc
     */
    public function addDriver(NewsletterDriverInterface $newsletter)
    {
        $this->drivers[$newsletter->getName()] = $newsletter;
    }


    /**
     * @inheritdoc
     */
    public function getDrivers()
    {
        return $this->drivers;
    }


    /**
     * @inheritdoc
     */
    public function getDriverByListId($listId)
    {
        $driversLists = $this->getLists();

        foreach ($driversLists as $driverName => $lists) {
            foreach ($lists as $list => $values) {
                if ($listId == $list) {
                    return $this->drivers[$driverName];
                }
            }
        }

        return null;
    }


    /**
     * @inheritdoc
     */
    public function getDriversNames()
    {
        return array_filter(
            $this->drivers,
            function (NewsletterDriverInterface $element) {
                return $element->getName();
            }

        );
    }


    /**
     * @inheritdoc
     */
    public function getDriverByName($name)
    {
        try {
            return $this->drivers[$name];
        } catch (\Exception $e) {
            throw new NewsletterException("Newsletter driver with name '$name' doesn't exist.");
        }
    }


    /**
     * @inheritdoc
     */
    public function getDriverNameFromList($listId)
    {
        $serviceLists = $this->getLists();

        foreach ($serviceLists as $service => $lists) {
            if (array_key_exists($listId, $lists)) {
                return $service;
            }
        }

        return null;
    }


    /**
     * @inheritdoc
     */
    public function subscribeUser(User $user, $listId)
    {
        $driver = $this->getDriverByListId($listId);
        $this->checkDriverConfiguration($driver);

        $newsletters = $this->em->getRepository('NecktieNewsletterBundle:Newsletter');
        $row = $newsletters->findBy(['listID' => $listId, 'user' => $user]);
        if ($row) {
            throw new NewsletterException(
                'This user('.$user->getId().') is already subscribed in list "'.$listId.'".'
            );
        }

        try {
            $this->em->beginTransaction();

            $s = $driver->subscribe($user, $listId);

            $n = new Newsletter();
            $n->setListID($listId);
            $n->setUser($user);
            $n->setMemberID($s);
            $n->setService($driver->getName());

            $this->em->persist($n);
            $this->em->flush();
            $this->em->commit();

            return $s;
        } catch (\Exception $ex) {
            $this->em->rollback();
            $this->logger->addWarning($ex);
        }

        return null;
    }


    /**
     * @inheritdoc
     */
    public function unsubscribeUser(User $user, $listId)
    {
        $driver = $this->getDriverByListId($listId);
        $this->checkDriverConfiguration($driver);

        $this->em->beginTransaction();

        try {
            $newsletter = $this->em->getRepository('NecktieNewsletterBundle:Newsletter');
            $row = $newsletter->findOneBy(['user' => $user, 'listID' => $listId]);

            if ($row) {
                $us = $driver->unsubscribe($row->getMemberID(), $listId);

                $this->em->remove($row);
                $this->em->flush();
                $this->em->commit();

                return $us;
            }
        } catch (\Exception $ex) {
            $this->em->rollback();
            $this->logger->addWarning($ex);
        }

        return null;
    }


    /**
     * @inheritdoc
     */
    public function updateUser(User $user)
    {
        $newsletter = $this->em->getRepository('NecktieNewsletterBundle:Newsletter');
        $lists = $newsletter->getUserLists($user);

        foreach ($lists as $driverName => $driverLists) {
            $driver = $this->drivers[$driverName];
            $driver->updateUser($user, $driverLists);
        }

    }


    /**
     * @inheritdoc
     */
    public function getLists($cache = true)
    {
        $id = 'newsletter.lists';

        if ($cache && $this->cache->get($id)) {
            return json_decode($this->cache->get($id), true);
        }

        foreach ($this->drivers as $driver) {
            try {
                $this->checkDriverConfiguration($driver);
            } catch (NewsletterException $ex) {
                unset($this->drivers[$driver->getName()]);
            }
        }

        $result = [];
        if (is_array($this->lists) && count($this->lists) > 0) {
            $result = $this->lists;
        } else {
            foreach ($this->drivers as $driver) {
                $result[$driver->getName()] = $driver->getLists();
            }
        }

        if ($cache) {
            $this->cache->set($id, json_encode($result));
        }

        return json_decode($this->cache->get($id), true);
    }


    /**
     * @param NewsletterDriverInterface $driver
     * @throws NewsletterException
     */
    private function checkDriverConfiguration(NewsletterDriverInterface $driver)
    {
        if (!$driver->isCorrectlyConfigured()) {
            throw new NewsletterException("Service is not correctly configured.");
        }
    }


    /**
     * @inheritdoc
     */
    public function checkDrivers()
    {
        $res = [];
        foreach ($this->drivers as $driver) {
            /** @var NewsletterDriverInterface $service */
            $res[$driver->getName()] = $driver->isCorrectlyConfigured();
        }

        return $res;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'newsletter';
    }

}
