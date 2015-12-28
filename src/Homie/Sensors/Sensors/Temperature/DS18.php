<?php

namespace Homie\Sensors\Sensors\Temperature;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use Homie\Sensors\Annotation\Sensor;
use Homie\Sensors\Definition;
use Homie\Sensors\Formatter\Temperature;
use Homie\Sensors\Interfaces\Searchable;
use Homie\Sensors\Sensors\AbstractSensor;
use Homie\Sensors\SensorVO;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.DS18.Temperature")
 */
class DS18 extends AbstractSensor implements Searchable
{

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
    public function getValue(SensorVO $sensor)
    {
        if (!$this->fileSystem->exists($sensor->parameter)) {
            return null;
        }

        $content = $this->fileSystem->fileGetContents($sensor->parameter);

        return $this->parseContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(SensorVO $sensor, OutputInterface $output)
    {
        if (!$this->fileSystem->exists($sensor->parameter)) {
            $output->writeln(
                sprintf(
                    '<error>%s: w1 bus not exists: %s</error>',
                    self::TYPE,
                    $sensor->parameter
                )
            );
            return false;
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function search()
    {
        return $this->glob->execGlob('/sys/bus/w1/devices/*/w1_slave');
    }

    /**
     * @return Definition
     */
    public function getDefinition()
    {
        $definition            = new Definition();
        $definition->type      = Definition::TYPE_TEMPERATURE;
        $definition->formatter = Temperature::TYPE;

        return $definition;
    }

    /**
     * @param string $content
     * @return float|null
     */
    protected function parseContent($content)
    {
        if (strpos($content, 'YES') === false) {
            // invalid response :(
            return null;
        }

        $matches = null;
        if (!preg_match('/t=([\-\d]+)$/', $content, $matches)) {
            return null;
        }

        $temperature = $matches[1] / 1000;

        $invalidTemperatures = [0.0, 85.0];
        if (in_array($temperature, $invalidTemperatures)) {
            return null;
        }

        return $this->round($temperature, 0.01);
    }
}
