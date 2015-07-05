<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Espeak\Espeak;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
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

    /**
     * @var Espeak|MockObject
     */
    private $espeak;

    public function setUp()
    {
        $this->espeak = $this->getMock(Espeak::class, [], [], '', false);
        $this->subject = new SpeakWidget($this->espeak);
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

        $expectedResult = '{"name":"Speak","description":"Speaks a given text.","parameters":{"title":{"name":"Title","type":"text","default":"Speak"},"speaker":{"name":"Speaker","values":null,"type":"single_select","default":"de+m1"}},"widgetId":"speak","width":4}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
