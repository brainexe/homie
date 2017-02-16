<?php

namespace Homie\Remote\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;

class SendEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;
    
    const SEND = 'remote.send';

    /**
     * @var string
     */
    private $code;

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
