<?php

namespace Tests\Homie\Webcam;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\IdGenerator;
use Homie\Webcam\ExpressionLanguage;
use Homie\Webcam\WebcamEvent;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;

/**
 * @covers Homie\Webcam\ExpressionLanguage
 */
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

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->idGenerator = $this->getMockWithoutInvokingTheOriginalConstructor(IdGenerator::class);
        $this->dispatcher  = $this->getMockWithoutInvokingTheOriginalConstructor(EventDispatcher::class);
        $this->subject     = new ExpressionLanguage();
        $this->subject->setEventDispatcher($this->dispatcher);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testTakePhoto()
    {
        $randomId = 100;

        $mailEvent = new WebcamEvent($randomId, WebcamEvent::TAKE_PHOTO);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($mailEvent);
        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->with('webcam')
            ->willReturn($randomId);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[0];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([]);
    }

    public function testTakeVideo()
    {
        $randomId = 100;

        $mailEvent = new WebcamEvent($randomId, WebcamEvent::TAKE_VIDEO, 5);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($mailEvent);
        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->with('webcam')
            ->willReturn($randomId);

        /** @var ExpressionFunction $function */
        $actual = iterator_to_array($this->subject->getFunctions());
        $function = $actual[1];
        $this->assertInstanceOf(ExpressionFunction::class, $function);

        $evaluator = $function->getEvaluator();
        $evaluator([], 5);
    }
}
