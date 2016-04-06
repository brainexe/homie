<?php

namespace Tests\Homie\Webcam;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Webcam\ExpressionLanguage;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

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
    private $eventDispatcher;

    public function setUp()
    {
        $this->eventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->subject         = new ExpressionLanguage();
        $this->subject->setEventDispatcher($this->eventDispatcher);

        $this->markTestIncomplete('todo');
    }
}
