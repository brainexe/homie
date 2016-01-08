<?php

namespace Homie\Expression\Command;

use BrainExe\Core\Annotations\Command as CommandAnnotation;
use BrainExe\Core\Traits\FileCacheTrait;
use Homie\Expression\Listener\WriteFunctionCache;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CommandAnnotation("Expression.Command.List")
 */
class ListFunctions extends SymfonyCommand
{

    use FileCacheTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('expression:list')
             ->setDescription('List all available expression functions');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $functions = $this->includeFile(WriteFunctionCache::CACHE);

        $table = new Table($output);
        $table->setHeaders(['Function', 'Parameters']);

        foreach ($functions as $function => $parameters) {
            $table->addRow([$function, implode(', ', $parameters)]);
        }

        $table->render();
    }
}
