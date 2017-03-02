<?php

namespace Homie\Switches\Change;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Translation\TranslationProvider;
use InvalidArgumentException;
use Generator;
use Homie\Switches\Gateway;
use Homie\Switches\SwitchInterface;
use Homie\Switches\VO\GpioSwitchVO;
use Homie\Switches\VO\RadioVO;
use Homie\Switches\VO\SwitchVO;

/**
 * @Service
 */
class Change implements SwitchInterface, TranslationProvider
{

    const TOKEN_NAME = 'switch.%s.name';

    /**
     * @var SwitchInterface[]
     */
    private $models = [];

    /**
     * @var Gateway
     */
    private $gateway;

    /**
     * @todo use ServiceLocator
     * @param Radio $radio
     * @param Gpio $gpio
     * @param Gateway $gateway
     */
    public function __construct(
        Radio $radio,
        Gpio $gpio,
        Gateway $gateway
    ) {
        $this->models[RadioVO::TYPE]      = $radio;
        $this->models[GpioSwitchVO::TYPE] = $gpio;
        $this->gateway = $gateway;
    }

    /**
     * @param SwitchVO $switch
     * @param int $status
     * @throws InvalidArgumentException
     */
    public function setStatus(SwitchVO $switch, int $status)
    {
        if (isset($this->models[$switch->type])) {
            $controller = $this->models[$switch->type];
        } else {
            throw new InvalidArgumentException(sprintf('Invalid switch type: %s', $switch->type));
        }

        $controller->setStatus($switch, $status);

        $switch->status = $status;
        $this->gateway->edit($switch);
    }

    /**
     * @return string[]|Generator
     */
    public static function getTokens()
    {
        foreach (SwitchVO::TYPES as $type) {
            yield sprintf(self::TOKEN_NAME, $type);
        }
    }
}
