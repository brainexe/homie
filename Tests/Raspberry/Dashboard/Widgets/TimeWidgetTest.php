<?php

namespace Tests\Raspberry\Dashboard\Widgets\TimeWidget;

use PHPUnit_Framework_TestCase;

use Raspberry\Dashboard\Widgets\TimeWidget;
use Raspberry\Dashboard\Widgets\WidgetMetadataVo;

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
        $actual_result = $this->subject->getId();
        $this->assertEquals(TimeWidget::TYPE, $actual_result);
    }

    public function testValidate()
    {
        $payload = [];

        $actual_result = $this->subject->validate($payload);
        $this->assertTrue($actual_result);
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

        $expectedResult = '{"name":"Time","parameters":[],"id":"time"}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
