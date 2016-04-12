<?php

namespace Tests\Homie\Switches;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\InputControl\Event;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Switches\ExpressionLanguage;
use Homie\Switches\SwitchChangeEvent;
use Homie\Switches\Switches;
use Homie\Switches\VO\RadioVO;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers Homie\Switches\ExpressionLanguage
 */
class ExpressionLanguageTest extends TestCase
{

    /**
     * @var ExpressionLanguage
     */
    private $subject;

    /**
     * @var Switches|MockObject
     */
    private $switches;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->switches   = $this->getMock(Switches::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new ExpressionLanguage($this->switches);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testEvaluator()
    {
        $switchId = 12333;
        $switchVo = new RadioVO();
        $status   = true;

        $this->switches
            ->expects($this->once())
            ->method('get')
            ->with($switchId)
            ->willReturn($switchVo);

        $event = new SwitchChangeEvent($switchVo, $status);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([], $switchId, $status);
    }
}
