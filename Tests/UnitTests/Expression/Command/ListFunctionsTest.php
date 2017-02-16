<?php

namespace Tests\Homie\Expression\Command;

use Homie\Expression\Command\ListFunctions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ListFunctionsTest extends TestCase
{

    /**
     * @var ListFunctions
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new ListFunctions();
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $commandTester = new CommandTester($this->subject);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        $this->assertContains('Function', $output);
    }
}
