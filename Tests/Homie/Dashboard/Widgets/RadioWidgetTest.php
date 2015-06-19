<?php

namespace Tests\Homie\Dashboard\Widgets;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\RadioWidget;
use Homie\Dashboard\Widgets\WidgetMetadataVo;
use Homie\Radio\Radios;
use Homie\Radio\VO\RadioVO;

/**
 * @covers Homie\Dashboard\Widgets\RadioWidget
 */
class RadioWidgetTest extends TestCase
{

    /**
     * @var RadioWidget
     */
    private $subject;

    /**
     * @var Radios|MockObject
     */
    private $radios;

    public function setUp()
    {
        $this->radios  = $this->getMock(Radios::class, [], [], '', false);
        $this->subject = new RadioWidget($this->radios);
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(RadioWidget::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $radio = new RadioVO();
        $radio->radioId = $radioId = 122;
        $radio->name    = 'radio';

        $this->radios
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn([
                 $radioId => $radio
             ]);

        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $radio = new RadioVO();
        $radio->radioId = $radioId = 122;
        $radio->name    = 'radio';

        $this->radios
            ->expects($this->once())
            ->method('getRadios')
            ->willReturn([
                $radioId => $radio
            ]);

        $actualResult = json_encode($this->subject);
        $expectedResult = '{"name":"Radio","parameters":{"radioId":{"name":"Radio ID","values":{"122":"radio"},"type":"single_select"}},"widgetId":"radio"}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
