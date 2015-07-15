<?php

namespace Homie\Sensors\Sensors;

use BrainExe\Annotations\Annotations\Inject;
use Homie\Client\ClientInterface;
use Homie\Sensors\Interfaces\Parameterized;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @link http://www.adafruit.com/product/386
 * @link https://learn.adafruit.com/dht
 */
abstract class AbstractDHT11 extends AbstractSensor implements Parameterized
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @Inject({"@HomieClient", "@FileSystem"})
     * @param ClientInterface $client
     * @param Filesystem $filesystem
     */
    public function __construct(
        ClientInterface $client,
        Filesystem $filesystem
    ) {
        $this->client     = $client;
        $this->filesystem = $filesystem;
    }

    /**
     * @param integer $pin
     * @return string
     */
    protected function getContent($pin)
    {
        $command = sprintf('timeout 3 %s', $pin);

        return $this->client->executeWithReturn($command);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($parameter, OutputInterface $output)
    {
        $file = explode(' ', $parameter)[0];

        if (!$this->filesystem->exists($file)) {
            $output->writeln(sprintf(
                '<error>%s: Script not exists: %s</error>',
                $this->getSensorType(),
                $parameter
            ));
            return false;
        }

        return true;
    }
}
