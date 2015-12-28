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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Misc.Metawear")
 */
class Metawear extends AbstractSensor
{

    const TYPE = 'custom.metawear';

    /**
     * @param integer $parameter
     * @return float
     */
    public function getValue($parameter)
    {
        $url = sprintf('http://localhost:8082/%s/', $parameter); // TODO load from node
        $content = file_get_contents($url);
        if ($content === false) {
            return null;
        }

        return (float)$content;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        return $this->getValue($parameter) !== null;
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
