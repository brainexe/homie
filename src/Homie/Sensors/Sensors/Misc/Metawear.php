<?php

namespace Homie\Sensors\Sensors\Misc;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor("Sensor.Misc.Metawear")
 */
class Metawear extends AbstractSensor implements Searchable
{

    const TYPE = 'custom.metawear';

    /**
     * @var string
     */
    private $url;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @Inject({"@HomieClient", "%metawear.url%"})
     * @param ClientInterface $client
     * @param string $url
     */
    public function __construct(
        ClientInterface $client,
        string $url
    ) {
        $this->client = $client;
        $this->url    = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor)
    {
        $url = sprintf('%s/%s/', $this->url, $sensor->parameter);
        $content = file_get_contents($url); // todo use client
        if ($content === false) {
            return null;
        }

        return (float)$content;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor) : bool
    {
        return $this->getValue($sensor) !== null;
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

    /**
     * @return string[]
     */
    public function search() : array
    {
        return [
            'temperature',
            'pressure',
            'brightness'
        ];
    }
}
