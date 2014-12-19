<?php

namespace Tests\Raspberry\Dashboard\Dashboard;

use PHPUnit_Framework_TestCase;

use Raspberry\Dashboard\WidgetFactory;
use Raspberry\Dashboard\WidgetInterface;
use Raspberry\Dashboard\Widgets\TimeWidget;

/**
 * @Covers Raspberry\Dashboard\Dashboard
 */
class WidgetFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var WidgetFactory
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new WidgetFactory();
        $this->subject->addWidget(new TimeWidget());
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
        $actualResult = $this->subject->getWidget(TimeWidget::TYPE);

        $this->assertTrue($actualResult instanceof WidgetInterface);
    }

    public function testGetWidgetTypes()
    {
        $actualResult = $this->subject->getAvailableWidgets();

        $this->assertEquals([new TimeWidget()], $actualResult);
    }
}
