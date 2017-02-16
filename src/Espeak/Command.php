<?php

namespace Homie\Espeak;

use BrainExe\Core\Annotations\Command as CommandAnnotation;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated really needed? use expression language
 * @CommandAnnotation("Espeak.Command")
 */
class Command extends SymfonyCommand
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
