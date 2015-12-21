<?php

namespace Homie\Radio\Change;

use BrainExe\Annotations\Annotations\Service;
use Homie\Radio\SwitchInterface;
use Homie\Radio\VO\RadioVO;
use Homie\Radio\VO\SwitchVO;

/**
 * @Service("Switches.Change.Gpio", public=false)
 */
class Gpio implements SwitchInterface
{

    /**
     * @param SwitchVO|RadioVO $switch
     * @param boolean $status
     */
    public function setStatus(SwitchVO $switch, $status)
    {
        // TODO implement GPIO switch
    }
}
