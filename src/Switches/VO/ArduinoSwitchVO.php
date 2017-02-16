<?php

namespace Homie\Switches\VO;

class ArduinoSwitchVO extends SwitchVO
{

    const TYPE = 'arduino';

    /**
     * @var int
     */
    public $pin;

    /**
     * @var int
     */
    public $nodeId;
}
