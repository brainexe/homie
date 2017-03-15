<?php

namespace Tests\Homie\Dashboard;

use PHPUnit\Framework\TestCase;
use Homie\Dashboard\WidgetFactory;
use Homie\Dashboard\WidgetInterface;
use Homie\Dashboard\Widgets\Time;

/**
 * @covers \Homie\Dashboard\WidgetFactory
 */
class WidgetFactoryTest extends TestCase
{

    /**
     * @var WidgetFactory
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new WidgetFactory([]);
        $this->subject->addWidget(Time::TYPE, new Time());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid widget: invalid
     */
    public function testGetInvalidWidget()
    {
        $this->subject->getWidget('invalid');
    }

    public function testGetValidWidget()
    {
        $actual = $this->subject->getWidget(Time::TYPE);

        $this->assertInstanceOf(WidgetInterface::class, $actual);
    }

    public function testSetWidgets()
    {
        $this->subject = new WidgetFactory(['type2' => new Time()]);

        $actual = $this->subject->getWidget('type2');
        $this->assertInstanceOf(WidgetInterface::class, $actual);
    }

    public function testGetWidgetTypes()
    {
        $actual = $this->subject->getAvailableWidgets();

        $this->assertEquals(['time' => new Time()], $actual);
    }
}
