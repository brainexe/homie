<?php

namespace Homie\Remote\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;

class SendEvent extends AbstractEvent implements PushViaWebsocket
{
    const SEND = 'remote.send';

    /**
     * @var string
     */
    public $code;

    /**
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(self::SEND);
        $this->code = $code;
    }
}
