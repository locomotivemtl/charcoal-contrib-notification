<?php

namespace Charcoal\Notification\Script;

use DateTime;
use Charcoal\Notification\Object\Notification;

// From 'charcoal-admin'

/**
 * Process "daily" notifications
 */
class ProcessDailyScript extends AbstractNotificationScript
{
    /**
     * Get the frequency type of this script.
     *
     * @return string
     */
    protected function frequency()
    {
        return 'daily';
    }

    /**
     * Retrieve the "minimal" date that the revisions should have been made for this script.
     * @return DateTime
     */
    protected function startDate()
    {
        $d = new DateTime('yesterday');
        $d->setTime(0, 0, 0);
        return $d;
    }

    /**
     * Retrieve the "maximal" date that the revisions should have been made for this script.
     * @return DateTime
     */
    protected function endDate()
    {
        $d = new DateTime('today');
        $d->setTime(0, 0, 0);
        return $d;
    }

    /**
     * @param Notification $notification The notification object.
     * @param array        $objects      The objects that were modified.
     * @return array
     */
    protected function emailData(Notification $notification, array $objects)
    {
        unset($notification, $objects);

        return [
            'subject'         => sprintf('Daily Charcoal Notification - %s', $this->startDate()->format('Y-m-d')),
            'template_ident'  => 'charcoal/notification/email/notification.daily',
            'template_data'   => [
                'startString' => $this->startDate()->format('Y-m-d')
            ]
        ];
    }
}
