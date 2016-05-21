<?php

namespace Homie\VoiceControl;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;

class VoiceEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const SPEECH = 'voice.text';

    /**
     * @var string
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        parent::__construct(self::SPEECH);
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getText() : string
    {
        return $this->text;
    }
}
