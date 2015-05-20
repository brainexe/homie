<?php

namespace Homie\Client;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use RuntimeException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service("HomieClient.Local", public=false)
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
    public function execute($command, array $arguments = [])
    {
        $this->executeWithReturn($command, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function executeWithReturn($command, array $arguments = [])
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
