<?php


namespace Necktie\NewsletterBundle\Drivers\Maropost;


use Necktie\AppBundle\Entity\User;
use Necktie\NewsletterBundle\Drivers\ConnectorInterface;
use Necktie\NewsletterBundle\NewsletterDriverInterface;


/**
 * Class Maropost
 * @package Necktie\NewsletterBundle\Drivers\Maropost
 */
class Maropost implements NewsletterDriverInterface
{

    private $connector;


    /**
     * Maropost constructor.
     * @param ConnectorInterface $connector
     */
    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }


    /**
     * @return ConnectorInterface
     */
    public function getConnector()
    {
        return $this->connector;
    }


    /**
     *
     * For subscribe and update user
     *
     * @param User $user
     * @param string $listId
     *
     * @return mixed
     */
    public function subscribe(User $user, $listId)
    {
        $contact = [];
        $contact['contact'] = [
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'phone' => $user->getPhoneNumber(),
        ];

        $apiCall = $this->connector->request("POST", "lists/$listId/contacts", json_encode($contact));

        if (is_array($apiCall) && array_key_exists('id', $apiCall)) {
            return $apiCall['id'];
        }

        return null;
    }


    /**
     * @param string $userId
     * @param string $listId
     * @return mixed|null
     */
    public function unsubscribe($userId, $listId)
    {
        $apiCall = $this->connector->request("DELETE", "lists/$listId/contacts/$userId");

        return $apiCall;
    }


    /** @return array */
    public function getLists()
    {
        $lists = [];
        $apiCall = $this->connector->request('GET', 'lists');

        foreach ($apiCall as $list) {
            $lists[$list['id']] = $list['name'];
        }

        return $lists;
    }


    /** @return string */
    public function getName()
    {
        return 'maropost';
    }


    /** @return bool */
    public function isCorrectlyConfigured()
    {
        $apiCall = $this->connector->request("GET", "lists");

        return is_array($apiCall);
    }


    /** @inheritdoc */
    function updateUser(User $user, array $lists = [])
    {
        $contact = [];
        $contact['contact'] = [
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'phone' => $user->getPhoneNumber(),
        ];

        try {
            $userId = $lists[0][0];
            $this->connector->request("PUT", "contacts/$userId", json_encode($contact));
        } catch (\Exception $ex) {
        }

        return null;
    }
}