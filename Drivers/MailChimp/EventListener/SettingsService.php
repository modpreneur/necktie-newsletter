<?php

namespace Necktie\NewsletterBundle\Drivers\MailChimp\EventListener;

use Necktie\AppBundle\Service\AppConfigManager;


/**
 * Class SettingsService.
 */
class SettingsService
{
    /** @var  AppConfigManager */
    private $config;


    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }


    /**
     * Settings.
     */
    public function registerSettings()
    {
        $this->config->registerSetting(
            'newsletter',
            'mailchimp',
            'apiKey',
            'MailChimp is not configured. Please set up this plugin via Navigation -> Settings -> MailChimp.'
        );
    }
}
