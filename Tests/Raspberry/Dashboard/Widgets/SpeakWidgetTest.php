<?php

namespace Tests\Raspberry\Dashboard\Widgets;

use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Dashboard\Widgets\SpeakWidget;
use Raspberry\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Raspberry\Dashboard\Widgets\SpeakWidget
 */
class SpeakWidgetTest extends TestCase
{

    /**
     * @var SpeakWidget
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new SpeakWidget();
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(SpeakWidget::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $actualResult = json_encode($this->subject);

        $expectedResult = '{"name":"Speak","parameters":[],"widgetId":"speak"}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
