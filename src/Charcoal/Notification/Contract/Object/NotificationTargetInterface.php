<?php

namespace Charcoal\Notification\Contract\Object;

interface NotificationTargetInterface
{
    /**
     * Object type
     *
     * @var string
     */
    public function type();

    /**
     * Objects label / name
     * L10n
     *
     * @var Translation|string
     */
    public function label();

    /**
     * @var boolean
     */
    public function active();
}
