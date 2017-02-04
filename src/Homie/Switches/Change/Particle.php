<?php

namespace Homie\Switches\Change;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Arduino\SerialEvent;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\ArduinoSwitchVO;
use Homie\Switches\VO\ParticleVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Change.Particle")
 */
class Particle implements SwitchInterface
{

    use EventDispatcherTrait;

    /**
     * @param SwitchVO|ParticleVO $switch
     * @param int $status
     */
    public function setStatus(SwitchVO $switch, int $status)
    {
        // TODO
        $this->dispatchInBackground($event);
    }
}
