<?php

namespace Homie\EggTimer;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Homie\Espeak\EspeakVO;

class EggTimerEvent extends AbstractEvent
{

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
