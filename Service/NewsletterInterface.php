<?php

namespace Necktie\NewsletterBundle\Service;

use Necktie\AppBundle\Entity\User;
use Necktie\NewsletterBundle\Exception\NewsletterException;
use Necktie\NewsletterBundle\NewsletterDriverInterface;


/**
 * Interface NewsletterInterface
 * @package Necktie\NewsletterBundle\Service
 */
interface NewsletterInterface
{

    /**
     * @param NewsletterDriverInterface $newsletter
     * @return mixed
     */
    function addDriver(NewsletterDriverInterface $newsletter);


    /**
     * @return \Necktie\NewsletterBundle\NewsletterDriverInterface[]
     */
    function getDrivers();


    /**
     * @return array string array
     */
    function getDriversNames();


    /**
     * @param string $name
     * @return NewsletterDriverInterface
     * @throws NewsletterException
     */
    function getDriverByName($name);


    /**
     * Return driver name from list id or list hash.
     *
     * @param string $listId
     *
     * @return string|null
     */
    function getDriverNameFromList($listId);


    /**
     * @param User $user
     * @param string $listId
     * @throws NewsletterException
     * @return string|null
     */
    function subscribeUser(User $user, $listId);


    /**
     * @param User $user
     * @param string $listId
     * @return null
     */
    function unsubscribeUser(User $user, $listId);


    /**
     * Check driver configuration
     * return
     * mailchimp => true, maropost => false, ...
     *
     * @return array
     */
    function checkDrivers();


    /**
     * Return string array
     *
     * mailchimp => [ 13jjg12 => List name, ... ], ...
     *
     * @param bool $cache
     * @return array
     */
    function getLists($cache = true);
}