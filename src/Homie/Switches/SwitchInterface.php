<?php

namespace Homie\Switches;

use Homie\Switches\VO\SwitchVO;

interface SwitchInterface
{

    /**
     * @param SwitchVO $switch
     * @param int $status
     */
    public function setStatus(SwitchVO $switch, $status);
}
