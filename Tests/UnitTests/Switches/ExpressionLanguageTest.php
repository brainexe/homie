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
    private $radios;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->radios     = $this->getMock(Switches::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new ExpressionLanguage($this->radios);
        $this->subject->setEventDispatcher($this->dispatcher);

        $this->markTestIncomplete("todo");
    }
}
