<?php

namespace Homie\Radio;

use BrainExe\Core\EventDispatcher\AbstractEvent;

use Homie\Radio\VO\SwitchVO;

class SwitchChangeEvent extends AbstractEvent
{

    const CHANGE_RADIO = 'switch.change';

    /**
     * @var SwitchVO
     */
    public $switch;

    /**
     * @var boolean
     */
    public $status;

    /**
     * @param SwitchVO $switchVo
     * @param bool $status
     */
    public function __construct(SwitchVO $switchVo, $status)
    {
        parent::__construct(self::CHANGE_RADIO);
        $this->switch = $switchVo;
        $this->status = $status;
    }
}
