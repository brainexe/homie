<?php

namespace Homie\Sensors\Sensors\Temperature;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Exception\InvalidSensorValueException;
use Homie\Sensors\Formatter\Temperature;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;

/**
 * @Sensor
 */
class OnBoard extends AbstractSensor implements Searchable
{

    const TYPE = 'temperature.onboard';

    const GLOB = '/sys/class/thermal/*/temp';

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * @var Glob
     */
    private $glob;

    /**
     * @Inject({"@FileSystem"})
     * @param FileSystem $filesystem
     * @param Glob $glob
     */
    public function __construct(FileSystem $filesystem, Glob $glob)
    {
        $this->fileSystem = $filesystem;
        $this->glob       = $glob;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(SensorVO $sensor) : float
    {
        $content = $this->fileSystem->fileGetContents($sensor->parameter);

        return $this->round($content / 1000, 0.01);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor) : bool
    {
        if (!$this->fileSystem->exists($sensor->parameter)) {
            $message = sprintf(
                '%s: Thermal zone file does not exist: %s',
                self::TYPE,
                $sensor->parameter
            );

            throw new InvalidSensorValueException($sensor, $message);
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function search() : array
    {
        return array_filter($this->glob->execGlob(self::GLOB), function ($file) {
            return strpos($file, 'cooling') === false;
        });
    }

    /**
     * @return Definition
     */
    public function getDefinition() : Definition
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_TEMPERATURE;
        $definition->formatter = Temperature::TYPE;

        return $definition;
    }
}
