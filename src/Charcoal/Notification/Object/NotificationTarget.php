<?php

namespace Charcoal\Admin\Object;

use Charcoal\Notification\NotificationTargetInterface;
use Charcoal\Object\Content;

/**
 *
 */
class NotificationTarget extends Content implements NotificationTargetInterface
{
    /**
     * Object type
     *
     * @var string
     */
    private $type;

    /**
     * Objects label / name
     * L10n
     *
     * @var Translation|string
     */
    private $label;

    /**
     * @var boolean
     */
    private $active = true;

    /**
     * ContentInput constructor.
     *
     * @param array|null $data Dependencies.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        $defaultData = $this->metadata()->defaultData();
        if ($defaultData) {
            $this->setData($defaultData);
        }
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Translation|string
     */
    public function label()
    {
        return $this->label;
    }

    /**
     * @param Translation|string $label
     * @return Notification
     */
    public function setLabel($label)
    {
        $this->label = $this->translator()->translation($label);
        return $this;
    }


    /**
     * @param boolean $active The active flag.
     * @return Notification Chainable
     */
    public function setActive($active)
    {
        $this->active = !!$active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function active()
    {
        return $this->active;
    }
}
