<?php

namespace Necktie\NewsletterBundle\EventListener;

use Necktie\AdminBundle\Entity\Setting;
use Necktie\AdminBundle\Event\SettingEvent;


class SettingListener
{


    /**
     * Add setting for Admin view
     *
     * @param SettingEvent $event
     *
     */
    public function addSetting(SettingEvent $event)
    {
        $event->addSetting(new Setting('Newsletters', 'tabs_settings_newsletters', 'ROLE_ADMIN_SETTING_NEWSLETTER'));
    }


}

