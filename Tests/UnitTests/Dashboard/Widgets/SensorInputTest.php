<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\SensorInput;
use Homie\Sensors\SensorGateway;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

/**
 * @covers Homie\Dashboard\Widgets\SensorInput
 */
class SensorInputTest extends TestCase
{

    /**
     * @var SensorInput
     */
    private $subject;

    /**
     * @var MockObject|SensorGateway
     */
    private $gateway;

    public function setUp()
    {
        $this->gateway = $this->createMock(SensorGateway::class);
        $this->subject = new SensorInput($this->gateway);
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(SensorInput::TYPE, $actualResult);
    }

    public function testSerialize()
    {
        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn([
                [
                    'sensorId' => '42',
                    'name'     => 'myname',
                ]
            ]);

        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVo::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn([]);

        $actualResult = json_encode($this->subject);
        $this->assertInternalType('string', $actualResult);
    }
}
