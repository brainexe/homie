<?php

namespace Homie\Radio\VO;

class RadioVO extends SwitchVO
{
    const TYPE = 'radio';

    /**
     * @var string
     */
    public $code;

    /**
     * @var int
     */
    public $pin;
}
