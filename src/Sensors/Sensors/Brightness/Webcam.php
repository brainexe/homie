<?php

namespace Homie\Sensors\Sensors\Brightness;

use BrainExe\Core\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor("Sensor.Brightness.Webcam")
 */
class Webcam extends AbstractSensor
{

    const TYPE = 'brightness.webcam';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject({"@HomieClient"})
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
        $tmpFile = tempnam('/tmp', self::TYPE);
        $this->client->executeWithReturn('fswebcam', [$tmpFile]);
        $command = sprintf(
            "convert %s -colorspace gray -resize 1x1 txt:-",
            $tmpFile
        );

        $result = $this->client->executeWithReturn($command);
        unlink($tmpFile);

        if (!preg_match('/gray\((\d+)\)/', $result, $matches)) {
            throw new InvalidSensorValueException($sensor, sprintf('No gray value found: %s', $result));
        }

        return $this->round($matches[1], 0.1);
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
