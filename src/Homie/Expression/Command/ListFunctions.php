<?php

namespace Homie\Expression\Command;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\Command as CommandAnnotation;
use Homie\Expression\Language;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CommandAnnotation("Expression.Command.List")
 * @codeCoverageIgnore
 */
class ListFunctions extends SymfonyCommand
{

    /**
     * @var Language
     */
    private $language;

    /**
     * @Inject("@Expression.Language")
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        parent::__construct();
        $this->language = $language;
    }

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
        $functions = $this->language->getFunctionNames();

        $table = new Table($output);
        $table->setHeaders(['Function']);

        foreach ($functions as $function) {
            $table->addRow([$function]);
        }

        $table->render();
    }
}
