<?php

namespace Raspberry\Client;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use RuntimeException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service("RaspberryClient.Local", public=false)
 */
class LocalClient implements ClientInterface
{

    const TIMEOUT = 3600;

    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @Inject("@ProcessBuilder")
     * @param ProcessBuilder $processBuilder
     */
    public function __construct(ProcessBuilder $processBuilder)
    {
        $this->processBuilder = $processBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command)
    {
        $this->executeWithReturn($command);
    }

    /**
     * {@inheritdoc}
     */
    public function executeWithReturn($command)
    {
        $process = $this->processBuilder
            ->add('')
            ->setTimeout(self::TIMEOUT)
            ->getProcess();

        $process->setCommandLine($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }
}
