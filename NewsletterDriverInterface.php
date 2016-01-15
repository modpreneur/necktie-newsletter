<?php

namespace Necktie\NewsletterBundle;

use Necktie\AppBundle\Entity\User;
use Necktie\NewsletterBundle\Drivers\ConnectorInterface;


/**
 * Interface IMail.
 */
interface NewsletterDriverInterface
{

    /** @return ConnectorInterface */
    public function getConnector();


    /**
     * @param User $user
     * @param string $listId
     *
     * @return string|null
     */
    function subscribe(User $user, $listId);


    /**
     * @param string $userId
     * @param string $listId
     * @return null
     */
    function unsubscribe($userId, $listId);


    /** @return array */
    function getLists();


    /** @return string */
    function getName();


    /** @return bool */
    function isCorrectlyConfigured();


    /**
     * @param User $user
     * @param array $lists
     * @return
     */
    function updateUser(User $user, array $lists = []);
}
