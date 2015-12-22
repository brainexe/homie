<?php

namespace Homie\Client;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\LoggerTrait;
use RuntimeException;

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
    public function execute($command, array $arguments = [])
    {
        $this->executeWithReturn($command, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function executeWithReturn($command, array $arguments = [])
    {
        $this->info(sprintf('LocalClient command: %s [%s]', $command, implode(' ', $arguments)));

        $process = $this->processBuilder
            ->setPrefix(explode(' ', $command))
            ->setArguments($arguments)
            ->setTimeout(self::TIMEOUT)
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException(
                'command: ' . $process->getCommandLine() . PHP_EOL .
                'status: ' . $process->getStatus() . PHP_EOL .
                'output: ' . $process->getErrorOutput() . $process->getOutput()
            );
        }

        $output = $process->getOutput();

        $this->debug(sprintf('LocalClient command output: %s [%s]: %s', $command, implode(' ', $arguments), $output));

        return $output;
    }
}
