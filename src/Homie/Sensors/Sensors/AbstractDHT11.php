<?php

namespace Homie\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Sensors\Interfaces\Parameterized;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @link http://www.adafruit.com/product/386
 * @link https://learn.adafruit.com/dht
 */
abstract class AbstractDHT11 extends AbstractSensor implements Parameterized
{
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
     * @param integer $pin
     * @return string
     */
    protected function getContent($pin)
    {
        return exec($pin, $result);
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
