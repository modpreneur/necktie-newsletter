<?php


namespace Necktie\NewsletterBundle\Drivers\Maropost;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Necktie\AppBundle\Service\AppConfigManager;
use Necktie\NewsletterBundle\Drivers\ConnectorInterface;
use Symfony\Bridge\Monolog\Logger;


/**
 * Class MaropostConnector
 * @package Necktie\NewsletterBundle\Drivers\Maropost
 */
class MaropostConnector implements ConnectorInterface
{

    private $logger;

    private $apiKey;
    private $accountId;

    private $url = "http://api.maropost.com/accounts/";


    /**
     * MaropostConnector constructor.
     * @param AppConfigManager $appConfigManager
     * @param Logger $logger
     */
    public function __construct(AppConfigManager $appConfigManager, Logger $logger)
    {
        $this->logger = $logger;

        $this->setApiKey(
            $appConfigManager->getSetting('newsletter', 'maropost', 'apiKey')
        );

        $this->setAccountId(
            $appConfigManager->getSetting('newsletter', 'maropost', 'accountId')
        );
    }


    /**
     * @inheritdoc
     */
    public function setSettings(array $args)
    {
        if (array_key_exists('accountId', $args)) {
            $this->setAccountId($args['accountId']);
        }

        if (array_key_exists('apiKey', $args)) {
            $this->setApiKey($args['apiKey']);
        }
    }


    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }


    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }


    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }


    /**
     * @param string $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }


    /**
     * @inheritdoc
     */
    public function request($method, $resources, $attributes = '')
    {
        $request = new Request(
            $method, $this->decorateUrl($resources), [
            "accept" => "application/json",
            "content-Type" => "application/json",
        ], $attributes
        );

        $res = (new Client())->send($request);

        return json_decode((string)$res->getBody(), true);
    }


    /**
     * @param string $type
     * @return string
     */
    private function decorateUrl($type)
    {
        $args = http_build_query(['auth_token' => $this->apiKey]);

        return $this->url.$this->accountId."/".$type."?".$args;
    }

}