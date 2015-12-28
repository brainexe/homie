<?php

namespace Homie\Sensors\Sensors\Misc;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @todo searchable (temperature / pressure / brigtness...)
 * @Sensor("Sensor.Misc.Metawear")
 */
class Metawear extends AbstractSensor
{

    const TYPE = 'custom.metawear';

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor)
    {
        $url = sprintf('http://localhost:8082/%s/', $sensor->parameter); // TODO load from node
        $content = file_get_contents($url);
        if ($content === false) {
            return null;
        }

        return (float)$content;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor, OutputInterface $output)
    {
        return $this->getValue($sensor->parameter) !== null;
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
