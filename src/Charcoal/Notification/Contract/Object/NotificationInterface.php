<?php

namespace Charcoal\Notification\Contract\Object;

interface NotificationInterface
{
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
