<?php

namespace Raspberry\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Util\FileSystem;
use Raspberry\Sensors\Annotation\Sensor;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @Sensor("Sensor.Temperature.OnBoard")
 */
class TemperatureOnBoardSensor implements SensorInterface
{

    const PATH = '/sys/class/thermal/thermal_zone0/temp';
    const TYPE = 'temperature_onboard';

    use TemperatureSensorTrait;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @Inject("@FileSystem")
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->fileSystem = $filesystem;
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
    public function getValue($pin)
    {
        $content = $this->fileSystem->fileGetContents($pin ?: self::PATH);

        return $content / 1000;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(OutputInterface $output)
    {
        if (!$this->fileSystem->exists(self::PATH)) {
            $output->writeln(sprintf(
                '<error>%s: Thermal zone file does not exist: %s</error>',
                self::getSensorType(),
                self::PATH
            ));
            return false;
        }

        return true;
    }
}
