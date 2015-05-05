<?php

namespace Homie\Arduino;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class SerialEvent extends AbstractEvent
{

    const SERIAL = 'arduino.serial';

    const SERVO   = 's';
    const ANALOG  = 'a';
    const DIGITAL = 'd';

    /**
     * @var string
     */
    private $action;

    /**
     * @var int
     */
    private $pin;

    /**
     * @var int
     */
    private $value;

    /**
     * @param string $action SerialEvent::*
     * @param int $pin
     * @param int $value
     */
    public function __construct($action, $pin, $value = 0)
    {
        $this->event_name = self::SERIAL;
        $this->action     = $action;
        $this->pin        = $pin;
        $this->value      = $value;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
}
