<?php

namespace Homie\Switches\VO;

abstract class SwitchVO
{

    const TYPE = 'unknown';

    /**
     * @var string
     */
    public $switchId;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * Current status of switch
     * @var int
     */
    public $status;

    public function __construct()
    {
        $this->type = static::TYPE;
    }
}
