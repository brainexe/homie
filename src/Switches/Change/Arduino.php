<?php

namespace Homie\Switches\Change;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Arduino\SerialEvent;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\ArduinoSwitchVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Change.Arduino")
 */
class Arduino implements SwitchInterface
{

    use EventDispatcherTrait;

    /**
     * @param SwitchVO|ArduinoSwitchVO $switch
     * @param int $status
     */
    public function setStatus(SwitchVO $switch, int $status)
    {
        $event = new SerialEvent(SerialEvent::DIGITAL, $switch->pin, $status);
        $this->dispatchInBackground($event);
    }
}
