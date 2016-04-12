<?php

namespace Homie\Remote\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class ReceivedEvent extends AbstractEvent implements PushViaWebsocket
{
    const RECEIVED = 'remote.received';

    /**
     * @var string
     */
    public $code;

    /**
     * @param string $code
     */
    public function __construct(string $code)
    {
        parent::__construct(self::RECEIVED);
        $this->code = $code;
    }
}
