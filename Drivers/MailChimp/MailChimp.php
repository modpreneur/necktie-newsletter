<?php

namespace Necktie\NewsletterBundle\Drivers\MailChimp;

use GuzzleHttp\Exception\ClientException;
use Necktie\AppBundle\Entity\User;
use Necktie\NewsletterBundle\Drivers\ConnectorInterface;
use Necktie\NewsletterBundle\Exception\NewsletterException;
use Necktie\NewsletterBundle\NewsletterDriverInterface;
use Nette\Utils\Strings;


/**
 * Class MailChimp.
 */
class MailChimp implements NewsletterDriverInterface
{

    /** @var  MailChimpConnector */
    private $connector;


    /**
     * @param ConnectorInterface $connector
     */
    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }


    /**
     * @return MailChimpConnector
     */
    public function getConnector()
    {
        return $this->connector;
    }


    /**
     * @param User $user
     * @param string $list
     *
     * @return string|null
     */
    public function subscribe(User $user, $list)
    {
        $data = [
            'email_address' => $user->getEmail(),
            'status' => 'subscribed',
        ];

        if ($user->getFirstName() && $user->getLastName()) {
            $data['merge_fields'] = [
                'FNAME' => $user->getFirstName(),
                'LNAME' => $user->getLastName(),
            ];
        }

        $request = $this->connector->request(
            "/lists/$list/members",
            $data,
            'POST'
        );

        if (is_array($request) && array_key_exists('id', $request)) {
            return $request['id'];
        }

        return null;
    }


    /**
     * @param string $userID
     * @param string $list
     *
     * @return string
     */
    public function unsubscribe($userID, $list)
    {
        return $this->connector->request("/lists/$list/members/$userID", [], 'DELETE');
    }


    /**
     * @return array
     */
    public function getLists()
    {
        $lists = $this->connector->request('/lists', [], 'GET');

        if (is_array($lists) && array_key_exists('lists', $lists)) {
            $lists = $lists['lists'];

            $filter = [];
            if ($lists === null) {
                return [];
            }

            foreach ($lists as $list) {
                $filter[$list['id']] = $list['name'];
            }

            return $filter;
        }
    }


    /** @return bool */
    public function isCorrectlyConfigured()
    {
        try {
            $res = $this->connector->request('/', [], 'GET');
            if (is_string($res) && Strings::contains($res, 'Error')) {
                return false;
            } else {
                return true;
            }
        } catch (NewsletterException $ex) {
            return false;
        } catch (ClientException $ex) {
            return false;
        }
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'mailchimp';
    }


    /** @inheritdoc */
    function updateUser(User $user, array $lists = [])
    {
        foreach ($lists as $list) {
            $userHash = $list[0];
            $listId = $list[1];
            ($this->connector->request(
                "/lists/$listId/members/$userHash",
                [
                    'email_address' => $user->getEmail(),
                    'merge_fields' => [
                        'FNAME' => $user->getFirstName(),
                        'LNAME' => $user->getLastName(),
                        'EMAIL' => "alois@novak.cz",
                    ],
                    'merge_vars' => [
                        'EMAIL' => "alois@novak.cz",
                    ],
                    'replace_interests' => false,

                ],
                "PATCH"
            ));
        }
    }
}
