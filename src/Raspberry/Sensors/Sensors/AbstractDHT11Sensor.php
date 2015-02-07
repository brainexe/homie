<?php

namespace Raspberry\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

abstract class AbstractDHT11Sensor implements SensorInterface
{

    const ADAFRUIT_SCRIPT = 'Adafruit_DHT';

    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var
     */
    private $adafruit;

    /**
     * @Inject({"@ProcessBuilder", "@FileSystem", "%adafruit.path%"})
     * @param ProcessBuilder $processBuilder
     * @param Filesystem $filesystem
     * @param string $adafruit
     */
    public function __construct(ProcessBuilder $processBuilder, Filesystem $filesystem, $adafruit)
    {
        $this->processBuilder = $processBuilder;
        $this->filesystem     = $filesystem;
        $this->adafruit       = $adafruit;
    }

    /**
     * @param integer $pin
     * @return string
     */
    protected function getContent($pin)
    {
        $command = sprintf('sudo %s 11 %d', self::ADAFRUIT_SCRIPT, $pin);

        $process = $this->processBuilder
            ->setArguments([$command])
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            return '';
        }

        return $process->getOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(OutputInterface $output)
    {
        $script = $this->adafruit . self::ADAFRUIT_SCRIPT;

        if (!$this->filesystem->exists($script)) {
            $output->writeln(sprintf(
                '<error>%s: ada script not exists: %s</error>',
                $this->getSensorType(),
                $script
            ));
            return false;
        }

        return true;
    }
}
