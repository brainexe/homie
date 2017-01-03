<?php

namespace Homie\EggTimer;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\Traits\JsonSerializableTrait;
use Homie\Espeak\EspeakVO;
use JsonSerializable;

class EggTimerEvent extends AbstractEvent implements JsonSerializable
{
    use JsonSerializableTrait;

    const DONE = 'egg_timer.done';

    /**
     * @var EspeakVO
     */
    private $espeak;

    /**
     * @param EspeakVO $espeak
     */
    public function __construct(EspeakVO $espeak = null)
    {
        parent::__construct(self::DONE);
        $this->espeak = $espeak;
    }

    /**
     * @return EspeakVO|null
     */
    public function getEspeak()
    {
        return $this->espeak;
    }
}
