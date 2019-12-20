<?php

namespace Charcoal\Notification\Service;

use Charcoal\Admin\User;
use Charcoal\App\AppConfig;
use Charcoal\Loader\CollectionLoaderAwareTrait;
use Charcoal\Model\ModelFactoryTrait;
use Charcoal\Notification\Object\Notification;
use Charcoal\Object\ObjectRevision;
use Charcoal\Presenter\Presenter;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;

class NotificationService
{
    use ModelFactoryTrait;
    use CollectionLoaderAwareTrait;

    /**
     * @var EmailFactory
     */
    protected $emailFactory;

    /**
     * @var Presenter
     */
    protected $notificationPresenter;

    /**
     * @var AppConfig
     */
    protected $appConfig;

    /**
     * @var string
     */
    protected $frequency = 'daily';

    /**
     * @var \DateTime|string
     */
    protected $startDate;

    /**
     * @var \DateTime|string
     */
    protected $endDate;

    /**
     * NotificationService constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setNotificationPresenter($data['notification/presenter']);
        $this->setEmailFactory($data['email/factory']);
        $this->setModelFactory($data['model/factory']);
        $this->setCollectionLoader($data['model/collection/loader']);
        $this->setAppConfig($data['config']);

        return $this;
    }

    /**
     * @return EmailFactory
     */
    public function emailFactory()
    {
        return $this->emailFactory;
    }

    /**
     * @param EmailFactory $emailFactory
     * @return NotificationService
     */
    public function setEmailFactory($emailFactory)
    {
        $this->emailFactory = $emailFactory;
        return $this;
    }

    /**
     * @return Presenter
     */
    public function notificationPresenter()
    {
        return $this->notificationPresenter;
    }

    /**
     * @param Presenter $notificationPresenter
     * @return NotificationService
     */
    public function setNotificationPresenter($notificationPresenter)
    {
        $this->notificationPresenter = $notificationPresenter;
        return $this;
    }

    /**
     * @return AppConfig
     */
    public function appConfig()
    {
        return $this->appConfig;
    }

    /**
     * @param AppConfig $appConfig
     * @return NotificationService
     */
    public function setAppConfig($appConfig)
    {
        $this->appConfig = $appConfig;
        return $this;
    }

    /**
     * @return string
     */
    public function frequency()
    {
        return $this->frequency;
    }

    /**
     * @param string $frequency
     * @return NotificationService
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;
        return $this;
    }

    /**
     * @param string|DateTimeInterface $ts The start date.
     * @throws InvalidArgumentException If the date is not a valid datetime format.
     * @return TimeGraphWidgetInterface Chainable
     */
    public function setStartDate($ts)
    {
        if (is_string($ts)) {
            try {
                $ts = new DateTime($ts);
            } catch (Exception $e) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid start date: %s',
                    $e->getMessage()
                ), $e);
            }
        }
        if (!($ts instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "Start Date" value. Must be a date/time string or a DateTime object.'
            );
        }
        $this->startDate = $ts;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function startDate()
    {
        return $this->startDate;
    }

    /**
     * @param string|DateTimeInterface $ts The end date.
     * @throws InvalidArgumentException If the date is not a valid datetime format.
     * @return TimeGraphWidgetInterface Chainable
     */
    public function setEndDate($ts)
    {
        if (is_string($ts)) {
            try {
                $ts = new DateTime($ts);
            } catch (Exception $e) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid end date: %s',
                    $e->getMessage()
                ), $e);
            }
        }
        if (!($ts instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid "End Date" value. Must be a date/time string or a DateTime object.'
            );
        }
        $this->endDate = $ts;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function endDate()
    {
        return $this->endDate;
    }

    /**
     * @return \ArrayAccess|\Charcoal\Model\ModelInterface[]
     */
    public function loadNotifications()
    {
        $proto  = $this->modelFactory()->create(Notification::class);
        $loader = $this->collectionLoader()->setModel($proto);

        $loader->addFilter('frequency', $this->frequency());

        return $loader->load();
    }

    /**
     * @param $user
     */
    public function notificationsByUser($user)
    {
        if (is_scalar($user)) {
            $user = $this->modelFactory()->create(User::class)->load($user);
        }

        if (!($user instanceof User)) {
            return [];
        }

        $proto  = $this->modelFactory()->create(Notification::class);
        $loader = $this->collectionLoader()->setModel($proto);

        $loader->addFilter('users', $user->id())
            ->addFilter('frequency', $this->frequency());

        $list = $loader->load();

        $out = [];
        foreach ($list as $notification) {
            if (empty($notification['targetTypes'])) {
                continue;
            }

            $objByType = [];
            $num       = 0;
            foreach ($notification['targetTypes'] as $objType) {
                $objects             = $this->updatedObjects($objType);
                $num                 += count($objects);
                $objByType[$objType] = $objects;
            }

            $out[] = [
                'notification' => $notification->id(),
                'num'          => $num,
                'objects'      => $objByType
            ];
        }

        return $out;
    }

    /**
     * Handle a notification request
     *
     * @param Notification $notification The notification object to handle.
     * @return void
     */
    public function handleNotification(Notification $notification, $lambda)
    {
        $objects = $this->objectsByNotification($notification);
        if (empty($objects)) {
            return;
        }

        $emailData = [];
        if (is_callable($lambda)) {
            $emailData = call_user_func($lambda, $notification, $objects['byType']);
        }

        $this->sendEmail($notification, $objects['byType'], $objects['total'], $emailData);
    }

    /**
     * Get updated objects by notification.
     *
     * @param Notification $notification
     * @return array
     */
    public function objectsByNotification(Notification $notification)
    {
        if (empty($notification['targetTypes'])) {
            return [];
        }
        $objectsByTypes = [];
        $numTotal       = 0;
        foreach ($notification['targetTypes'] as $objType) {
            $objType = trim($objType);
            $objects = $this->updatedObjects($objType);
            $num     = count($objects);
            if ($num == 0) {
                continue;
            }
            $obj              = [];
            $obj['objects']   = $objects;
            $obj['num']       = $num;
            $obj['type']      = $objType;
            $obj['typeLabel'] = isset($objects[0]['targetTypeLabel']) ? $objects[0]['targetTypeLabel'] : $objType;

            $objectsByTypes[$objType] = $obj;
            $numTotal                 += $num;
        }

        return [
            'total' => $numTotal,
            'byType' => $objectsByTypes
        ];
    }

    /**
     * @param Notification $notification The notification object.
     * @param array        $objects      The objects that were modified.
     * @param integer      $numTotal     Total number of modified objects.
     * @return void
     */
    private function sendEmail(Notification $notification, array $objects, $numTotal, $emailData = [])
    {
        if ($numTotal == 0) {
            return;
        }

        $email = $this->emailFactory()->create('email');

        $defaultEmailData = [
            'campaign'      => 'admin-notification-' . $notification->id(),
            'subject'       => 'Charcoal Notification',
            'from'          => 'charcoal@example.com',
            'template_data' => [
                'objects'     => new \ArrayIterator($objects),
                'numObjects'  => $numTotal,
                'frequency'   => $this->frequency(),
                'startString' => $this->startDate()->format('Y-m-d H:i:s'),
                'endString'   => $this->endDate()->format('Y-m-d H:i:s')
            ]
        ];

        $dataFromConfig = $this->appConfig()->get('notification.email_data');


        $emailData = array_replace_recursive($defaultEmailData, $dataFromConfig, $emailData);

        $email->setData($emailData);

        foreach ($notification['users'] as $userId) {
            $user = $this->modelFactory()->create(User::class);
            $user->load($userId);
            if (!$user->id() || !$user->email()) {
                continue;
            }
            $email->addTo($user->email());
        }

        foreach ($notification['extraEmails'] as $extraEmail) {
            if (filter_var($extraEmail, FILTER_VALIDATE_EMAIL)) {
                $email->addBcc($extraEmail);
            }
        }

        $email->send();
    }

    /**
     * @param string $objType The object (target) type to process.
     * @return CollectionInterface
     */
    private function updatedObjects($objType)
    {
        $loader = $this->collectionLoader()->reset();
        $loader->setModel(ObjectRevision::class)
            ->addFilter([
                'property' => 'target_type',
                'value'    => $objType
            ])
            ->addFilter([
                'property' => 'rev_ts',
                'value'    => $this->startDate()->format('Y-m-d H:i:s'),
                'operator' => '>'
            ])
            ->addFilter([
                'property' => 'rev_ts',
                'value'    => $this->endDate()->format('Y-m-d H:i:s'),
                'operator' => '<'
            ])
            ->addOrder([
                'property' => 'rev_ts',
                'mode'     => 'DESC'
            ]);

        $list = $loader->load();
        $out  = [];
        foreach ($list as $l) {
            $out[] = $this->notificationPresenter()->transform($l);
        }

        return $out;
    }

}
