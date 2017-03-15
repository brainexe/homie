<?php

namespace Homie\Sensors\Sensors\Barometer;

use BrainExe\Core\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\Barometer;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor
 * @link https://learn.adafruit.com/using-the-bmp085-with-homie-pi/using-the-adafruit-bmp-python-library
 * @link http://www.adafruit.com/product/1603
 */
class BMP085 extends AbstractSensor implements Parameterized
{

    const TYPE = 'barometer.bmp085';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject("@HomieClient")
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        $content = $this->client->executeWithReturn($sensor->parameter);

        if (!empty($content) && preg_match('/Pressure: (.*?) hPa/', $content, $matches)) {
            $pressure = trim($matches[1]);
            return (float)$pressure;
        }

        throw new InvalidSensorValueException($sensor, sprintf('Invalid response: %s', $content));
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_BAROMETER;
        $definition->formatter = Barometer::TYPE;
        $definition->unit      = 'hPa';

        return $definition;
    }
}
