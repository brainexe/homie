<?php

namespace Homie\Switches\Change;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Translation\TranslationProvider;
use Exception;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\RadioVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service("Switches.Change.Change", public=false)
 */
class Change implements SwitchInterface, TranslationProvider
{

    const TOKEN_NAME = 'switch.%s.name';

    /**
     * @var SwitchInterface[]
     */
    private $models = [];

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
        $this->models[RadioVO::TYPE]      = $radio;
        $this->models[GpioSwitchVO::TYPE] = $gpio;
    }

    /**
     * @param SwitchVO $switch
     * @param bool $status
     * @throws Exception
     */
    public function setStatus(SwitchVO $switch, $status)
    {
        if (isset($this->models[$switch->type])) {
            $controller = $this->models[$switch->type];
        } else {
            throw new Exception(sprintf('Invalid switch type: %s', $switch->type));
        }

        $controller->setStatus($switch, $status);
    }

    /**
     * @return string[]
     */
    public static function getTokens()
    {
        $types = [
            GpioSwitchVO::TYPE,
            RadioVO::TYPE
        ];

        foreach ($types as $type) {
            yield sprintf(self::TOKEN_NAME, $type);
        }
    }
}
