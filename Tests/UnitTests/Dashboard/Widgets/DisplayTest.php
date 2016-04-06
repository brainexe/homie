<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\Display;
use Homie\Display\Gateway;
use Homie\Display\Settings;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Homie\Dashboard\Widgets\Display
 */
class DisplayTest extends TestCase
{

    /**
     * @var Display
     */
    private $subject;

    /**
     * @var MockObject|Gateway
     */
    private $gateway;

    public function setUp()
    {
        $this->gateway = $this->getMock(Gateway::class, [], [], '', false);
        $this->subject = new Display($this->gateway);
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(Display::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $display = new Settings();
        $display->displayId = 'test';

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([
                $display
            ]);

        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $actualResult = json_encode($this->subject);
        $this->assertInternalType('string', $actualResult);
    }
}
