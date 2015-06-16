<?php

namespace Homie\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\None;
use Homie\Sensors\Formatter\Temperature;
use Homie\Sensors\Interfaces\Parameterized;
use Homie\Sensors\Interfaces\Searchable;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Script")
 */
class Script extends AbstractSensor implements Parameterized
{

    const TYPE = 'script';

    /**
     * @Inject({})
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($parameter)
    {
        exec($parameter, $output);

        return implode(PHP_EOL, $output);
    }

    /**
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
        $definition->name      = gettext('Script Execution');
        $definition->type      = Definition::TYPE_NONE;
        $definition->formatter = None::TYPE;

        return $definition;
    }
}
