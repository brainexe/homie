<?php

namespace Raspberry\Media;

use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service(public=false)
 */
class Sound
{

    const COMMAND = 'mplayer';

    /**
     * @var ProcessBuilder
     */
    private $processBuilder;

    /**
     * @inject("@ProcessBuilder")
     * @param ProcessBuilder $processBuilder
     */
    public function __construct(ProcessBuilder $processBuilder)
    {
        $this->processBuilder = $processBuilder;
    }

    /**
     * @param string $file
     */
    public function playSound($file)
    {
        $process = $this->processBuilder
            ->setArguments([self::COMMAND, $file])
            ->getProcess();

        $process->run();
    }
}
