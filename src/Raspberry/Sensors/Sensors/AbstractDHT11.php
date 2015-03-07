<?php

namespace Raspberry\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use Raspberry\Sensors\Interfaces\Sensor;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @link http://www.adafruit.com/product/386
 * @link https://learn.adafruit.com/dht
 */
abstract class AbstractDHT11 implements Sensor
{

    // todo use $pin only
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
    public function __construct(
        ProcessBuilder $processBuilder,
        Filesystem $filesystem,
        $adafruit
    ) {
        $this->processBuilder = $processBuilder;
        $this->filesystem     = $filesystem;
        $this->adafruit       = $adafruit;
    }

    /**
     * @todo use Client
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
    public function isSupported($parameter, OutputInterface $output)
    {
        if (!$this->filesystem->exists($parameter)) {
            $output->writeln(sprintf(
                '<error>%s: ada script not exists: %s</error>',
                $this->getSensorType(),
                $parameter
            ));
            return false;
        }

        return true;
    }
}
