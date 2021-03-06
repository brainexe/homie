<?php

namespace Homie\Client\Adapter;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\LoggerTrait;
use Homie\Client\ClientInterface;
use RuntimeException;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service("HomieClient.Local")
 */
class LocalClient implements ClientInterface
{
    use LoggerTrait;

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
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function execute(string $command, array $arguments = []) : void
    {
        $process = $this->processBuilder
            ->setPrefix(explode(' ', $command))
            ->setArguments($arguments)
            ->setTimeout(self::TIMEOUT)
            ->getProcess();
        $process->start();

        $this->info(sprintf('LocalClient command: %s', $process->getCommandLine()));
    }

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws LogicException
     */
    public function executeWithReturn(string $command, array $arguments = []) : string
    {
        $process = $this->processBuilder
            ->setPrefix(explode(' ', $command))
            ->setArguments($arguments)
            ->setTimeout(self::TIMEOUT)
            ->getProcess();
        $process->run();

        $this->checkOutput($process);

        $output = $process->getOutput();

        $this->debug(sprintf('LocalClient command output: %s: %s', $process->getCommandLine(), $output));

        return $output;
    }

    /**
     * @param Process $process
     * @throws \RuntimeException
     * @throws LogicException
     */
    private function checkOutput(Process $process)
    {
        $this->info(sprintf('LocalClient command: %s', $process->getCommandLine()));

        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                'command: ' . $process->getCommandLine() . PHP_EOL .
                'status: ' . $process->getStatus() . PHP_EOL .
                'output: ' . $process->getErrorOutput() .
                $process->getOutput()
            );
        }
    }
}
