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
    public $espeak;

    /**
     * @param EspeakVO $espeak
     */
    public function __construct(EspeakVO $espeak = null)
    {
        parent::__construct(self::DONE);
        $this->espeak = $espeak;
    }
}
