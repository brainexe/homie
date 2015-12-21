<?php

namespace Homie\Switches\Change;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Exception;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\RadioVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Change.Change", public=false)
 */
class Change implements SwitchInterface
{

    /**
     * @var Radio
     */
    private $radio;

    /**
     * @var Gpio
     */
    private $gpio;

    /**
     * @Inject({
     *     "@Switches.Change.Radio",
     *     "@Switches.Change.Gpio",
     * })
     * @param Radio $radio
     * @param Gpio $gpio
     */
    public function __construct(
        Radio $radio,
        Gpio $gpio
    ) {
        $this->radio = $radio;
        $this->gpio  = $gpio;
    }

    /**
     * @param SwitchVO $switch
     * @param bool $status
     * @throws Exception
     */
    public function setStatus(SwitchVO $switch, $status)
    {
        /** @var SwitchInterface $controller */
        $controller = null;
        switch ($switch->type) {
            case RadioVO::TYPE:
                $controller = $this->radio;
                break;
            case GpioSwitchVO::TYPE:
                $controller = $this->gpio;
                break;
            default:
                throw new Exception(sprintf('Invalid switch type: %s', $switch->type));
        }

        $controller->setStatus($switch, $status);
    }
}
