<?php

namespace Homie\Client\Adapter;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\LoggerTrait;
use Homie\Client\ClientInterface;
use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service("HomieClient.Local", public=false)
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
     */
    public function execute(string $command, array $arguments = [])
    {
        $this->executeWithReturn($command, $arguments);
    }

    /**
     * {@inheritdoc}
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
