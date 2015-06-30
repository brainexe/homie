<?php

namespace Tests\Homie\Dashboard\Widgets;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\SpeakWidget;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Homie\Dashboard\Widgets\SpeakWidget
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

        $expectedResult = '{"name":"Speak","description":"Speaks a given text.","parameters":[],"widgetId":"speak","width":4}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
