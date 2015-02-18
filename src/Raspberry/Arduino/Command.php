<?php

namespace Raspberry\Arduino;

use BrainExe\Core\Annotations\Command as CommandAnnotation;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 * @CommandAnnotation("Command.Arduino")
 */
class Command extends SymfonyCommand
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('arduino:serial')
            ->setDescription('Send data via serial')
            ->addArgument('action', InputArgument::REQUIRED, 'Action')
            ->addArgument('pin', InputArgument::REQUIRED, 'Pin')
            ->addArgument('value', InputArgument::REQUIRED, 'Value');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $pin    = $input->getArgument('pin');
        $value  = $input->getArgument('value');

        $event = new SerialEvent($action, $pin, $value);

        $this->dispatchEvent($event);

        $output->writeln('<info>done</info>');
    }
}
