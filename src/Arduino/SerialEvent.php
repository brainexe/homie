<?php

namespace Homie\Arduino;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;

class SerialEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const SERIAL = 'arduino.serial';

    const SERVO   = 's';
    const ANALOG  = 'a';
    const DIGITAL = 'd';
    const LCD     = 'lcd';

    /**
     * @var string
     */
    private $action;

    /**
     * @var int
     */
    private $pin;

    /**
     * @var int|string
     */
    private $value;

    /**
     * @param string $action SerialEvent::*
     * @param int $pin
     * @param int $value
     */
    public function __construct(string $action, int $pin, $value = 0)
    {
        parent::__construct(self::SERIAL);
        $this->action     = $action;
        $this->pin        = $pin;
        $this->value      = $value;
    }

    /**
     * @return string
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * @return int
     */
    public function getPin() : int
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
