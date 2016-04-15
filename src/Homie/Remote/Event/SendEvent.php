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
    public function __construct(string $code)
    {
        parent::__construct(self::SEND);
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode() : string
    {
        return $this->code;
    }
}
