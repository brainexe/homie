<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\StatusWidget;
use Homie\Dashboard\Widgets\WebcamWidget;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Homie\Dashboard\Widgets\WebcamWidget
 */
class WebcamWidgetTest extends TestCase
{

    /**
     * @var StatusWidget
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new WebcamWidget();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(WebcamWidget::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $actualResult = json_encode($this->subject);

        $expectedResult = '{"name":"Webcam","description":"Take shots","parameters":[],"widgetId":"webcam","width":4}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
