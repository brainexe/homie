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
 * @Sensor("Sensor.DS18.Temperature")
 */
class DS18 extends AbstractSensor implements Searchable
{

    use TemperatureTrait;

    const TYPE = 'temperature.ds18';

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * @var Glob
     */
    private $glob;

    /**
     * @Inject({"@FileSystem", "@Glob"})
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
        if (!$this->fileSystem->exists($sensor->parameter)) {
            throw new InvalidSensorValueException($sensor, sprintf('Invalid file: %s', $sensor->parameter));
        }

        $content = $this->fileSystem->fileGetContents($sensor->parameter);

        return $this->parseContent($sensor, $content);
    }

    /**
     * @return string[]
     */
    public function search() : array
    {
        return $this->glob->execGlob('/sys/bus/w1/devices/*/w1_slave');
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

    /**
     * @param SensorVO $sensor
     * @param string $content
     * @return float
     * @throws InvalidSensorValueException
     */
    protected function parseContent(SensorVO $sensor, string $content) : float
    {
        if (strpos($content, 'YES') === false) {
            // invalid response :(
            throw new InvalidSensorValueException($sensor, sprintf('Invalid content: %s', $content));
        }

        $matches = null;
        if (!preg_match('/t=([\-\d]+)$/', $content, $matches)) {
            throw new InvalidSensorValueException($sensor, sprintf('Invalid content: %s', $content));
        }

        $temperature = $matches[1] / 1000;

        if (!$this->validateValue($temperature)) {
            throw new InvalidSensorValueException($sensor, sprintf('Invalid content: %s', $content));
        }

        return $this->round($temperature, 0.01);
    }

    /**
     * @param float $temperature
     * @return bool
     */
    private function validateValue(float $temperature) : bool
    {
        $invalidTemperatures = [0.0, 85.0, 127.937];
        if (in_array($temperature, $invalidTemperatures)) {
            return false;
        }

        return $this->validateTemperature($temperature);
    }
}
