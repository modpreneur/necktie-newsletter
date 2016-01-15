<?php

namespace Necktie\NewsletterBundle\Tests;

use Doctrine\ORM\EntityManager;
use Necktie\AppBundle\Entity\User;
use Necktie\NewsletterBundle\NewsletterDriverInterface;
use Necktie\NewsletterBundle\Service\NewsletterInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


/**
 * Class NewsletterTest.
 */
class NewsletterTest extends KernelTestCase
{

    // settings

    private $driverServicesName = [
        'mailchimp' => 'necktie.newsletter.driver.mailchimp',
        'maropost' => 'necktie.newsletter.driver.maropost',
    ];

    private $lists = [
        'mailchimp' => [
            '935b98a010' => 'Developer Academy',
            '5ae21679f5' => 'Moderni Podnikatel',
            'b12e728b80' => 'Brno',
            'ea05df7387' => 'Necktie test list',
        ],
        'maropost' => [
            '32190' => 'Test list 1',
        ],
    ];

    private $listsIds = ['32190', 'ea05df7387'];

    // end settings

    private $drivers = [];

    /** @var  EntityManager */
    private $em;

    /** @var  NewsletterInterface */
    private $newsletter;

    /** @var  User */
    private $user;


    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();

        $this->user = $this->em->getRepository('NecktieAppBundle:User')->find(1);

        $this->newsletter = static::$kernel->getContainer()->get('necktie.newsletter');

        foreach ($this->driverServicesName as $name => $sName) {
            $this->drivers[$name] = static::$kernel->getContainer()->get($sName);
        }
    }


    public function testGetLists()
    {
        $newsletter = static::$kernel->getContainer()->get('necktie.newsletter');

        $this->assertTrue(
            $this->arrays_are_similar(
                $this->lists,
                $newsletter->getLists(false)
            )
        );

    }


    public function testSubscribeAndUnsubscribeUser()
    {
        foreach ($this->listsIds as $id) {
            $result = $this->newsletter->subscribeUser($this->user, $id);
            $this->assertNotEmpty($result);
            $this->assertTrue(strlen($result) >= 9);

            // 9 maropost
            // 32 mailchimp
        }

        foreach ($this->listsIds as $id) {
            $this->newsletter->unsubscribeUser($this->user, $id);
        }
    }


    /**
     * @expectedException \Necktie\NewsletterBundle\Exception\NewsletterException
     *
     */
    public function testAlreadySubscribed()
    {

        foreach ($this->listsIds as $id) {
            $this->newsletter->subscribeUser($this->user, $id);
        }

        foreach ($this->listsIds as $id) {
            $this->newsletter->subscribeUser($this->user, $id);
        }
    }


    /**
     * @runInSeparateProcess
     */
    public function testIfConfigIsCorrectlyConfigured()
    {

        foreach ($this->listsIds as $id) {
            $this->newsletter->unsubscribeUser($this->user, $id);
        }

        /** @var NewsletterDriverInterface $driver */
        foreach ($this->drivers as $driver) {
            $connector = $driver->getConnector();
            $this->assertTrue($driver->isCorrectlyConfigured());
            $connector->setSettings(['apiKey' => 'blabol']);
            $this->assertFalse($driver->isCorrectlyConfigured());
        }
    }


    /**
     * @param array $expected
     * @param array $actual
     * @return bool
     */
    public function arrays_are_similar(array $expected, array $actual)
    {
        $expected = json_encode($expected);
        $actual = json_encode($actual);

        return $expected == $actual;
    }


    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
