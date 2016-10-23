<?php

namespace Homie\Switches;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\PushViaWebsocket;
use BrainExe\Core\Traits\JsonSerializableTrait;
use Homie\Switches\VO\SwitchVO;

class SwitchChangeEvent extends AbstractEvent implements PushViaWebsocket
{
    use JsonSerializableTrait;

    const CHANGE = 'switch.change';

    /**
     * @var SwitchVO
     */
    private $switch;

    /**
     * @var int
     */
    private $status;

    /**
     * @param SwitchVO $switchVo
     * @param int $status
     */
    public function __construct(SwitchVO $switchVo, int $status)
    {
        parent::__construct(self::CHANGE);
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
    public function getSwitch() : SwitchVO
    {
        return $this->switch;
    }
}
