<?php

namespace Homie\Switches;

use BrainExe\Core\EventDispatcher\AbstractEvent;

use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use Homie\Switches\VO\SwitchVO;

class SwitchChangeEvent extends AbstractEvent implements PushViaWebsocket
{

    const CHANGE_RADIO = 'switch.change';

    /**
     * @var SwitchVO
     */
    public $switch;

    /**
     * @var int
     */
    public $status;

    /**
     * @param SwitchVO $switchVo
     * @param int $status
     */
    public function __construct(SwitchVO $switchVo, int $status)
    {
        parent::__construct(self::CHANGE_RADIO);
        $this->switch = $switchVo;
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * @return SwitchVO
     */
    public function getSwitch()
    {
        return $this->switch;
    }
}
