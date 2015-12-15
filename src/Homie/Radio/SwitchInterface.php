<?php

namespace Homie\Radio;

use Homie\Radio\VO\SwitchVO;

interface SwitchInterface
{

    /**
     * @param SwitchVO $switch
     * @param int $status
     */
    public function setStatus(SwitchVO $switch, $status);
}
