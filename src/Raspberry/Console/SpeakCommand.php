<?php

namespace Raspberry\Console;

use BrainExe\Core\Annotations\Command as CommandAnnotation;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Raspberry\Espeak\EspeakEvent;

use Raspberry\Espeak\EspeakVO;

/**
 * @CommandAnnotation
 */
class SpeakCommand extends Command
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('speak')
            ->setDescription('Speak via espeak')
            ->addArgument('text');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text     = $input->getArgument('text');
        $espeakVo = new EspeakVO($text);
        $event    = new EspeakEvent($espeakVo);

        $this->dispatchEvent($event);
    }
}
