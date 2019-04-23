<?php

namespace Charcoal\Notification\Contract\Object;

interface NotificationInterface
{
    /**
     * The user ids.
     *
     * @var string[]
     */
    public function users();

    /**
     * The types of object to watch, for notifications.
     *
     * @var string[]
     */
    public function targetTypes();

    /**
     * Extra emails the report shoul be sent to.
     *
     * @var string[]
     */
    public function extraEmails();

    /**
     * Can be "minute", "hourly", "daily", "weekly" or "monthly".
     *
     * @var string
     */
    public function frequency();

    /**
     * @var boolean
     */
    public function active();
}
