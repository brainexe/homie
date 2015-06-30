<?php

namespace Tests\Homie\Dashboard\Widgets\TimeWidget;

use PHPUnit_Framework_TestCase;

use Homie\Dashboard\Widgets\TimeWidget;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

class TimeWidgetTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TimeWidget
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new TimeWidget();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(TimeWidget::TYPE, $actualResult);
    }

    public function testValidate()
    {
        $payload = [];

        $actualResult = $this->subject->validate($payload);
        $this->assertTrue($actualResult);
    }

    public function testCreate()
    {
        $payload = [];

        $this->subject->create($payload);
    }

    public function testSerialize()
    {
        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $actualResult = json_encode($this->subject);

        $expectedResult = '{"name":"Time","description":"Displays the current time","parameters":[],"widgetId":"time","width":4}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
