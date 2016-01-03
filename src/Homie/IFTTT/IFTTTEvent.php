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
     * @param string $type
     * @param string $eventName
     */
    public function __construct($type, $eventName)
    {
        parent::__construct($type);
        $this->event = $eventName;
    }
}
