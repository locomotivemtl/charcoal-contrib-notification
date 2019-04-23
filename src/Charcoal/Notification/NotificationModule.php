<?php

namespace Charcoal\Notification;

// from charcoal-app
use Charcoal\App\Module\AbstractModule;

/**
 * Notification Module
 */
class NotificationModule extends AbstractModule
{
    const ADMIN_CONFIG = 'vendor/locomotivemtl/charcoal-contrib-notification/config/admin.json';
    const APP_CONFIG = 'vendor/locomotivemtl/charcoal-contrib-notification/config/config.json';

    /**
     * Setup the module's dependencies.
     *
     * @return AbstractModule
     */
    public function setup()
    {
        $container = $this->app()->getContainer();

        $notificationServiceProvider = new NotificationServiceProvider();
        $container->register($notificationServiceProvider);

        $notificationConfig = $container['notification/config'];
        $this->setConfig($notificationConfig);

        return $this;
    }

}
