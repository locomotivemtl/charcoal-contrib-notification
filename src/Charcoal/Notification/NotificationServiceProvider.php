<?php

namespace Charcoal\Notification;

// local dependencies.
use Charcoal\Notification\Service\NotificationService;
use Charcoal\Notification\Transformer\NotificationTransformer;
use Charcoal\Presenter\Presenter;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

// from pimple

/**
 * Notification Service Provider
 */
class NotificationServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container Pimple DI container.
     * @return void
     */
    public function register(Container $container)
    {
        /**
         * @return NotificationConfig
         */
        $container['notification/config'] = function () {
            return new NotificationConfig();
        };


        /**
         * @param Container $container Pimple DI container.
         * @return Presenter
         */
        $container['notification/presenter'] = function (Container $container) {
            return new Presenter(new NotificationTransformer([
                    'model/factory' => $container['model/factory'],
                    'base-url'      => $container['base-url']
                ])
            );
        };

        /**
         * @param Container $container Pimple DI container.
         * @return Presenter
         */
        $container['notification'] = function (Container $container) {
            return new NotificationService([
                'model/factory'           => $container['model/factory'],
                'model/collection/loader' => $container['model/collection/loader'],
                'email/factory'           => $container['email/factory'],
                'notification/presenter'  => $container['notification/presenter']
            ]);
        };
    }
}
