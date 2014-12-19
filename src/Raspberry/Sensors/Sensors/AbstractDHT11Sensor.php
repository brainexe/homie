<?php

namespace Raspberry\Sensors\Sensors;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

abstract class AbstractDHT11Sensor implements SensorInterface
{

    const ADA_SCRIPT = '/opt/Adafruit-Raspberry-Pi-Python-Code/Adafruit_DHT_Driver/Adafruit_DHT';

    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @Inject({"@ProcessBuilder", "@FileSystem"})
     * @param ProcessBuilder $processBuilder
     * @param Filesystem $filesystem
     */
    public function __construct(ProcessBuilder $processBuilder, Filesystem $filesystem)
    {
        $this->processBuilder = $processBuilder;
        $this->filesystem = $filesystem;
    }

    /**
     * @param integer $pin
     * @return string
     */
    protected function getContent($pin)
    {
        $command = sprintf('sudo %s 11 %d', self::ADA_SCRIPT, $pin);

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
        if (!$this->filesystem->exists(self::ADA_SCRIPT)) {
            $output->writeln(sprintf(
                '<error>%s: ada script not exists: %s</error>',
                $this->getSensorType(),
                self::ADA_SCRIPT
            ));
            return false;
        }

        return true;
    }
}
