<?php

namespace Homie\Sensors\Sensors;

use Homie\Sensors\CompilerPass\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Formatter;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Barometer")
 * @link https://learn.adafruit.com/using-the-bmp085-with-homie-pi/using-the-adafruit-bmp-python-library
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
        $definition->name      = gettext('Barometer');
        $definition->type      = Definition::TYPE_BAROMETER;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
