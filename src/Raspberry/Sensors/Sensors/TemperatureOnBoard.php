<?php

namespace Raspberry\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Util\FileSystem;
use BrainExe\Core\Util\Glob;
use Raspberry\Sensors\Annotation\Sensor;
use Raspberry\Sensors\Interfaces\Searchable;
use Symfony\Component\Console\Output\OutputInterface;
use Raspberry\Sensors\Interfaces\Sensor as SensorInterface;

/**
 * @Sensor("Sensor.Temperature.OnBoard")
 */
class TemperatureOnBoard implements SensorInterface, Searchable
{

    const TYPE = 'temperature_onboard';

    use TemperatureTrait;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var Glob
     */
    private $glob;

    /**
     * @Inject({"@FileSystem", "@Glob"})
     * @param Filesystem $filesystem
     * @param Glob $glob
     */
    public function __construct(Filesystem $filesystem, Glob $glob)
    {
        $this->fileSystem = $filesystem;
        $this->glob       = $glob;
    }

    /**
     * {@inheritdoc}
     */
    public function getSensorType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($parameter)
    {
        $content = $this->fileSystem->fileGetContents($parameter);

        return $content / 1000;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        if (!$this->fileSystem->exists($parameter)) {
            $output->writeln(
                sprintf(
                    '<error>%s: Thermal zone file does not exist: %s</error>',
                    self::TYPE,
                    $parameter
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
        return $this->glob->glob('/sys/class/thermal/');
    }
}
