<?php

namespace Raspberry\Radio\VO;

use BrainExe\Core\Util\AbstractVO;

class RadioVO extends AbstractVO
{

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $code;

    /**
     * @var integer
     */
    public $pin;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;
}
