<?php

namespace Necktie\NewsletterBundle\Drivers\MailChimp;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Necktie\AppBundle\Service\AppConfigManager;
use Necktie\NewsletterBundle\Drivers\ConnectorInterface;


/**
 * Class MailChimpConnector.
 *
 * Error 400 - user probably registred
 *
 */
class MailChimpConnector implements ConnectorInterface
{
    /**
     * Endpoint for Mailchimp API v3.
     *
     * @var string
     */
    private $endpoint = 'https://<us>.api.mailchimp.com/3.0';

    /** @var string */
    private $apiKey;


    /**
     * @param AppConfigManager $appConfigManager
     */
    public function __construct(AppConfigManager $appConfigManager)
    {
        $this->apiKey = $appConfigManager->getSetting('newsletter', 'mailchimp', 'apiKey');

        if ($this->apiKey === null) {
            return;
        }

        if (strstr($this->apiKey, '-')) {
            list(, $dc) = explode('-', $this->apiKey);

            $this->endpoint = str_replace('<us>', $dc, $this->endpoint);
        } else {
            $this->apiKey = null;
        }
    }


    /**
     * @inheritdoc
     *
     */
    public function setSettings(array $args)
    {
        if (array_key_exists('apiKey', $args)) {
            $this->apiKey = $args['apiKey'];
        }
    }


    /**
     * @inheritdoc
     */
    public function request($resource, $arguments = [], $method = 'GET')
    {
        $method = strtoupper($method);
        $resource = $this->decorateResource($resource, $arguments, $method);

        /** @var \GuzzleHttp\Client $client */
        $client = new Client();
        $request = new Request(
            $method, $this->endpoint.$resource, [
                'Authorization' => 'apikey '.$this->apiKey,
                'Content-type' => 'application/json',
            ], (count($arguments) > 0) ? json_encode($arguments) : null
        );

        $res = $client->send($request);

        return json_decode((string)$res->getBody(), true);
    }


    /**
     * @param $resource
     * @param $arguments
     * @param $method
     *
     * @return string
     */
    private function decorateResource($resource, $arguments, $method)
    {
        if (strtolower($method) == 'get') {
            $resource .= '?'.http_build_query($arguments);
        }

        return $resource;
    }


    /**
     * Return endpoint.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
