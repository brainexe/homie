<?php

namespace Raspberry\Sensors\Sensors;

use Raspberry\Sensors\CompilerPass\Annotation\Sensor;
use Raspberry\Sensors\Definition;
use Raspberry\Sensors\Formatter\Formatter;
use Raspberry\Sensors\Formatter\None;
use Raspberry\Sensors\Interfaces\Parameterized;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Barometer")
 * @link https://learn.adafruit.com/using-the-bmp085-with-raspberry-pi/using-the-adafruit-bmp-python-library
 * @link http://www.adafruit.com/product/1603
 */
class Barometer extends AbstractSensor implements Parameterized
{

    const TYPE = 'barometer';

    /**
     * @todo
     * @param integer $parameter
     * @return double
     */
    public function getValue($parameter)
    {
        unset($parameter);
        return 0;
    }

    /**
     * @todo
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        return true;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = _('Barometer');
        $definition->type      = Definition::TYPE_BAROMETER;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
