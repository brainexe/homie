<?php

namespace Homie\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Barometer;
use Homie\Sensors\Interfaces\Parameterized;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.BMP085Barometer")
 * @link https://learn.adafruit.com/using-the-bmp085-with-homie-pi/using-the-adafruit-bmp-python-library
 * @link http://www.adafruit.com/product/1603
 */
class BMP085Barometer extends AbstractSensor implements Parameterized
{

    const TYPE = 'bmp085_barometer';

    /**
     * @Inject("@HomieClient")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param integer $parameter
     * @return double
     */
    public function getValue($parameter)
    {
        $content = $this->client->executeWithReturn($parameter);

        if (!$content) {
            return null;
        }

        if (preg_match('/Pressure: (.*?) hPa/', $content, $matches)) {
            $pressure = trim($matches[1]);
            return (float)$pressure;
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        return is_file($parameter);
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->name      = gettext('BMP085 Barometer');
        $definition->type      = Definition::TYPE_BAROMETER;
        $definition->formatter = Barometer::TYPE;

        return $definition;
    }
}
