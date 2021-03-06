<?php

namespace Homie\Sensors\Sensors\Decibel;

use BrainExe\Core\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor
 */
class Webcam extends AbstractSensor
{

    const TYPE = 'decibel.webcam';

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
        $tmpFile = sys_get_temp_dir() . '/tmp_rec.wav';
        $this->client->executeWithReturn('arecord', ['-d', 2, $tmpFile]);

        $content = $this->client->executeWithReturn('sox', ['-t', '.wav', $tmpFile, '-n', 'stat']);

        if (!preg_match('/^Maximum amplitude:\s*([\d\.]+?)$/m', $content, $match)) {
            throw new InvalidSensorValueException($sensor, sprintf('No Maximum amplitude found: %s', $content));
        }

        $value = (float)trim($match[1]);
        $value = 20 * log($value) / log(10);

        return $this->round($value, 0.01);
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;
        $definition->unit      = 'dB';

        return $definition;
    }
}
