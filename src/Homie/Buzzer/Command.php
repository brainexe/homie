<?php

namespace Homie\Buzzer;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BrainExe\Core\Annotations\Command as CommandAnnotation;

/**
 * @CommandAnnotation("Buzzer.Command")
 * @codeCoverageIgnore
 */
class Command extends SymfonyCommand
{
    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('buzzer.buzz')
            ->setDescription('Trigger buzzer');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $event = new Event();
        $this->dispatchEvent($event);
    }
}
