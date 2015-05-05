<?php

namespace Tests\Homie\Dashboard\Widgets;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\SensorWidget;
use Homie\Dashboard\Widgets\WidgetMetadataVo;
use Homie\Sensors\SensorGateway;

/**
 * @covers Homie\Dashboard\Widgets\SensorWidget
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
    private $gateway;

    public function setUp()
    {
        $this->gateway = $this->getMock(SensorGateway::class);
        $this->subject = new SensorWidget($this->gateway);

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
        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn([]);

        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $sensors = [
            [
                'name' => 'sensor name',
                'sensorId' => 12
            ]
        ];

        $this->gateway
            ->expects($this->once())
            ->method('getSensors')
            ->willReturn($sensors);

        $actualResult = json_encode($this->subject);

        $expectedResult =
            '{"name":"Sensor","parameters":{"sensor_id":'.
            '{"name":"Sensor ID","values":{"12":"sensor name"}}},"widgetId":"sensor"}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
