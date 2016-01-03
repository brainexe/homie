<?php

namespace Homie\IFTTT;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class IFTTTEvent extends AbstractEvent implements PushViaWebsocket
{
    const TRIGGER = 'ifttt.trigger';
    const ACTION  = 'ifttt.action';

    /**
     * @var string
     */
    public $event;

    /**
     * @var string
     */
    public $value1;

    /**
     * @var string
     */
    public $value2;

    /**
     * @var string
     */
    public $value3;

    /**
     * @param string $type
     * @param string $eventName
     * @param $value1
     * @param $value2
     * @param $value3
     */
    public function __construct(
        $type,
        $eventName,
        $value1 = null,
        $value2 = null,
        $value3 = null
    ) {
        parent::__construct($type);
        $this->event = $eventName;

        $this->value1 = $value1;
        $this->value2 = $value2;
        $this->value3 = $value3;
    }
}
