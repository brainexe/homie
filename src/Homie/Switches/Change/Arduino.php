<?php

namespace Homie\Switches\Change;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Arduino\SerialEvent;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\RadioVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Change.Arduino", public=false)
 */
class Arduino implements SwitchInterface
{

    use EventDispatcherTrait;

    /**
     * @param SwitchVO|RadioVO $switch
     * @param boolean $status
     */
    public function setStatus(SwitchVO $switch, $status)
    {
        $event = new SerialEvent(SerialEvent::DIGITAL, $switch->pin, $status);
        $this->dispatchInBackground($event);
    }
}
