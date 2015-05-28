<?php

namespace Homie\Buzzer;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class Event extends AbstractEvent implements PushViaWebsocket
{
    const TYPE = 'buzzer.buzz';

    public function __construct()
    {
        parent::__construct(self::TYPE);
    }
}
