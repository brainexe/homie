<?php

namespace Tests\Homie\Dashboard\Widgets;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\EggTimerWidget;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Homie\Dashboard\Widgets\EggTimerWidget
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

        $expectedResult = '{"name":"Egg Timer","description":"Simple egg timer","parameters":[],"widgetId":"egg_timer"}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
