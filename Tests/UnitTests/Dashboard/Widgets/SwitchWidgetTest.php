<?php

namespace Tests\Homie\Dashboard\Widgets;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\SwitchWidget;
use Homie\Dashboard\Widgets\WidgetMetadataVo;
use Homie\Switches\Switches;
use Homie\Switches\VO\RadioVO;

class SwitchWidgetTest extends TestCase
{

    /**
     * @var SwitchWidget
     */
    private $subject;

    /**
     * @var Switches|MockObject
     */
    private $radios;

    public function setUp()
    {
        $this->radios  = $this->createMock(Switches::class);
        $this->subject = new SwitchWidget($this->radios);
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(SwitchWidget::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $radio = new RadioVO();
        $radio->switchId = $switchId = 122;
        $radio->name     = 'radio';

        $this->radios
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                 $switchId => $radio
             ]);

        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVo::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $radio = new RadioVO();
        $radio->switchId = $switchId = 122;
        $radio->name     = 'radio';

        $this->radios
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                $switchId => $radio
            ]);

        $actual = json_encode($this->subject);
        $this->assertInternalType('string', $actual);
    }
}
