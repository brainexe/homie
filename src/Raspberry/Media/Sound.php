<?php

namespace Raspberry\Media;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Service(public=false)
 */
class Sound
{

    const COMMAND = 'mplayer %s';

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
     * @param string $file
     */
    public function playSound($file)
    {
        $process = $this->processBuilder
            ->add('')
            ->getProcess();

        $process->setCommandLine(sprintf(self::COMMAND, $file));
        $process->run();
    }
}
