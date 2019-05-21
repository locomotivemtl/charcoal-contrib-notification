<?php

namespace Charcoal\Notification\Script;

use Charcoal\Notification\Object\Notification;
use DateTime;

/**
 * Process "hourly" notifications
 */
class ProcessHourlyScript extends AbstractNotificationScript
{
    /**
     * Get the frequency type of this script.
     *
     * @return string
     */
    protected function frequency()
    {
        return 'hourly';
    }

    /**
     * Retrieve the "minimal" date that the revisions should have been made for this script.
     *
     * @return DateTime
     */
    protected function startDate()
    {
        $d = new DateTime('1 hour ago');
        $d->setTime($d->format('H'), 0, 0);
        return $d;
    }

    /**
     * Retrieve the "maximal" date that the revisions should have been made for this script.
     *
     * @return DateTime
     */
    protected function endDate()
    {
        $d = new DateTime('now');
        $d->setTime($d->format('H'), 0, 0);
        return $d;
    }

    /**
     * @param Notification $notification The notification object.
     * @param array        $objects      The objects that were modified.
     * @return array
     */
    public function emailData(Notification $notification, array $objects)
    {
        unset($notification, $objects);

        return [
            'subject'        => sprintf('Hourly Charcoal Notification - %s', $this->startDate()->format('Y-m-d H:m')),
            'template_ident' => 'charcoal/admin/email/notification.hourly',
            'template_data'  => [
                'startString' => $this->startDate()->format('Y-m-d H:m'),
            ]
        ];
    }
}
