<?php

namespace Tests\Homie\Expression\Controller;

use BrainExe\Core\Cron\Expression;
use BrainExe\Core\EventDispatcher\CronEvent;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use Homie\Expression\Controller\Cron;
use Homie\Expression\Controller\Variables;
use Homie\Expression\Variable;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Predis\Client;
use Symfony\Component\HttpFoundation\Request;

class VariablesTest extends TestCase
{

    /**
     * @var Variables
     */
    private $subject;

    /**
     * @var Variable|MockObject
     */
    private $variable;

    public function setup()
    {
        $this->variable = $this->createMock(Variable::class);

        $this->subject = new Variables(
            $this->variable
        );
    }

    public function testAdd()
    {
        $array = ['key' => 'value'];

        $this->variable
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($array);

        $actual = $this->subject->getAll();

        $this->assertEquals($array, $actual);
    }

    public function testDelete()
    {
        $this->variable
            ->expects($this->once())
            ->method('deleteVariable')
            ->with('key');

        $actual = $this->subject->deleteVariable(new Request(), 'key');

        $this->assertEquals(true, $actual);
    }

    public function testSetVariable()
    {
        $this->variable
            ->expects($this->once())
            ->method('setVariable')
            ->with('key', 'value');

        $actual = $this->subject->setVariable(new Request(), 'key', 'value');

        $this->assertEquals(true, $actual);
    }

}
