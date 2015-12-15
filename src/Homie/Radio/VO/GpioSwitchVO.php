<?php

namespace Homie\Radio\VO;

class GpioSwitchVO extends SwitchVO
{

    const TYPE = 'gpio';

    /**
     * @var int
     */
    public $pin;

    /**
     * @var int
     */
    public $nodeId;
}
