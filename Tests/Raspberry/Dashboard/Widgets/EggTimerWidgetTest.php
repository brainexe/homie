<?php

namespace Tests\Raspberry\Dashboard\Widgets;

use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Dashboard\Widgets\EggTimerWidget;
use Raspberry\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Raspberry\Dashboard\Widgets\EggTimer
 */
class EggTimerWidgetTest extends TestCase
{

    /**
     * @var EggTimerWidget
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new EggTimerWidget();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(EggTimerWidget::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {

        $actualResult = json_encode($this->subject);

        $expectedResult = '{"name":"Egg Timer","parameters":[],"widgetId":"egg_timer"}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
