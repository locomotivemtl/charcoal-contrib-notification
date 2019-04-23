<?php

namespace Charcoal\Notification\Script;

// From PSR-7
use Charcoal\Admin\AdminScript;
use Charcoal\App\Script\CronScriptInterface;
use Charcoal\App\Script\CronScriptTrait;
use Charcoal\Factory\FactoryInterface;
use Charcoal\Loader\CollectionLoaderAwareTrait;
use Charcoal\Model\ModelFactoryTrait;
use Charcoal\Notification\Object\Notification;
use Charcoal\Notification\Service\NotificationService;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for all the notification script
 */
abstract class AbstractNotificationScript extends AdminScript implements CronScriptInterface
{
    use CronScriptTrait;
    use CollectionLoaderAwareTrait;
    use ModelFactoryTrait;

    /**
     * @var FactoryInterface
     */
    private $emailFactory;

    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * @param RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        unset($request);

        $this->startLock();

        $climate = $this->climate();

        $frequency = $this->frequency();

        $this->notificationService()->setFrequency($frequency);
        $this->notificationService()->setStartDate($this->startDate());
        $this->notificationService()->setEndDate($this->endDate());

        $notifications = $this->notificationService()->loadNotifications();

        if (!$notifications) {
            return $response;
        }

        foreach ($notifications as $notification) {
            $objects = $this->notificationService()->objectsByNotification($notification);
            $this->climate()->green()->out(strtr('<white>%num</white> object(s) for notification <white>%notificationId</white>', [
                '%num'            => $objects['total'],
                '%notificationId' => $notification->id()
            ]));

            foreach ($objects['byType'] as $type => $obj) {
                foreach ($obj['objects'] as $o) {
                    $this->climate()->green()->out(strtr(
                        'Sending <white>%type</white> with ID: <white>%id</white>',
                        [
                            '%id'   => $o['targetId'],
                            '%type' => $o['targetTypeLabel']
                        ]
                    ));
                }
            }
            
            $this->notificationService()->handleNotification($notification, [$this, 'emailData']);
        }

        $this->stopLock();

        return $response;
    }

    /**
     * @param Container $container Pimple DI container.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);
        $this->setNotificationService($container['notification']);
    }

    /**
     * @return NotificationService
     */
    public function notificationService()
    {
        return $this->notificationService;
    }

    /**
     * @param NotificationService $notificationService
     * @return AbstractNotificationScript
     */
    public function setNotificationService($notificationService)
    {
        $this->notificationService = $notificationService;
        return $this;
    }

    /**
     * Get the frequency type of this script.
     *
     * @return string
     */
    abstract protected function frequency();

    /**
     * Retrieve the "minimal" date that the revisions should have been made for this script.
     *
     * @return DateTime
     */
    abstract protected function startDate();

    /**
     * Retrieve the "maximal" date that the revisions should have been made for this script.
     *
     * @return DateTime
     */
    abstract protected function endDate();

    /**
     * @param Notification $notification The notification object.
     * @param array        $objects      The objects that were modified.
     * @return array
     */
    abstract protected function emailData(Notification $notification, array $objects);

}
