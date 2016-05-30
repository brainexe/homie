<?php

namespace Tests\Homie\IFTTT;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\IFTTT\Event\TriggerEvent;
use Homie\IFTTT\ExpressionLanguage;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Request;

class ExpressionLanguageTest extends TestCase
{

    /**
     * @var ExpressionLanguage
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new ExpressionLanguage();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetFunctions()
    {
        $eventName = 'my-test';

        $event = new TriggerEvent($eventName, 2, 3);

        $request = new Request();
        $request->query->set('event', $eventName);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $actual = $this->subject->getFunctions();

        /** @var callable $function */
        $function = $actual[0]->getEvaluator();
        $function([], $eventName, 2, 3);

        $this->assertInternalType('array', $actual);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Function "triggerIFTTT" is not allowed as trigger
     */
    public function testCompiler()
    {
        /** @var callable $compiler */
        $compiler = $this->subject->getFunctions()[0]->getCompiler();

        $compiler([]);
    }
}
