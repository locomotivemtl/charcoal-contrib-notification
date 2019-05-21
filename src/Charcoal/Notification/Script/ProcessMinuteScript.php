<?php

namespace Charcoal\Notification\Script;

use Charcoal\Notification\Object\Notification;
use DateTime;

// From 'charcoal-admin'

/**
 * Process "minute" notifications
 */
class ProcessMinuteScript extends AbstractNotificationScript
{
    /**
     * Get the frequency type of this script.
     *
     * @return string
     */
    protected function frequency()
    {
        return 'minute';
    }

    /**
     * Retrieve the "minimal" date that the revisions should have been made for this script.
     *
     * @return DateTime
     */
    protected function startDate()
    {
        $d = new DateTime('-1 minute');
        $d->setTime($d->format('H'), $d->format('i'));
        return $d;
    }

    /**
     * Retrieve the "maximal" date that the revisions should have been made for this script.
     *
     * @return DateTime
     */
    protected function endDate()
    {
        // $d = new DateTime($this->startDate() . ' +1 minute');
        $d = new DateTime('now');
        $d->setTime($d->format('H'), $d->format('i'));
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
            'subject'        => sprintf('Daily Charcoal Notification - %s', $this->startDate()->format('Y-m-d H:i:s')),
            'template_ident' => 'charcoal/admin/email/notification.minute',
            'template_data'  => [
                'startString' => $this->startDate()->format('Y-m-d H:i:s')
            ]
        ];
    }
}
