<?php

namespace Homie\Switches\VO;

class ParticleVO extends SwitchVO
{

    const TYPE = 'particle';

    /**
     * @var string
     */
    public $function;

    /**
     * @var int
     */
    public $nodeId;
}
