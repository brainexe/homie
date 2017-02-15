<?php

namespace Homie\IFTTT\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;

class TriggerEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const TRIGGER = 'ifttt.trigger';

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
     * @param string $eventName
     * @param $value1
     * @param $value2
     * @param $value3
     */
    public function __construct(
        $eventName,
        $value1 = null,
        $value2 = null,
        $value3 = null
    ) {
        parent::__construct(self::TRIGGER);

        $this->event  = $eventName;
        $this->value1 = $value1;
        $this->value2 = $value2;
        $this->value3 = $value3;
    }
}
