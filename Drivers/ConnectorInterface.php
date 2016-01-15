<?php


namespace Necktie\NewsletterBundle\Drivers;

/**
 * Interface ConnectorInterface
 * @package Necktie\NewsletterBundle\Drivers\MailChimp
 */
interface ConnectorInterface
{

    /**
     * @param string $method (GET, POST, UPDATE, ...)
     * @param string $resource
     * @param string $attributes
     * @return string|null
     */
    function request($method, $resource, $attributes = '');


    /**
     * @param array $args
     * @return void
     */
    function setSettings(array $args);

}