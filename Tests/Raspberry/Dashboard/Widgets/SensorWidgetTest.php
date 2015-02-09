<?php

namespace Tests\Raspberry\Dashboard\Widgets;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Dashboard\Widgets\SensorWidget;
use Raspberry\Dashboard\Widgets\WidgetMetadataVo;
use Raspberry\Sensors\SensorGateway;

/**
 * @Covers Raspberry\Dashboard\Widgets\SensorWidget
 */
class SensorWidgetTest extends TestCase
{

    /**
     * @var SensorWidget
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $mockSensorGateway;

    public function setUp()
    {
        $this->mockSensorGateway = $this->getMock(SensorGateway::class);
        $this->subject           = new SensorWidget($this->mockSensorGateway);

    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(SensorWidget::TYPE, $actualResult);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage No sensor_id passe
     */
    public function testCreateWithoutSensorId()
    {
        $payload = [];

        $this->subject->create($payload);
    }

    public function testCreate()
    {
        $payload = [
                'sensor_id' => 1
        ];

        $this->subject->create($payload);
    }

    public function testSerialize()
    {
        $this->mockSensorGateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn([]);

        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $this->mockSensorGateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn([]);

        $actualResult = json_encode($this->subject);

        $expectedResult = '{"name":"Sensor","parameters":{"sensor_id":{"name":"Sensor ID","values":[]}},"widgetId":"sensor"}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
