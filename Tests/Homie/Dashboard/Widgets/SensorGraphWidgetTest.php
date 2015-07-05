<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\SensorGraphWidget;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\SensorWidget;
use Homie\Dashboard\Widgets\WidgetMetadataVo;
use Homie\Sensors\SensorGateway;

/**
 * @covers Homie\Dashboard\Widgets\SensorGraphWidget
 */
class SensorGraphWidgetTest extends TestCase
{

    /**
     * @var SensorGraphWidget
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->gateway = $this->getMock(SensorGateway::class);
        $this->subject = new SensorGraphWidget($this->gateway);
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(SensorGraphWidget::TYPE, $actualResult);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage No sensor_ids passed
     */
    public function testCreateWithoutSensorId()
    {
        $payload = [];

        $this->subject->create($payload);
    }

    public function testCreate()
    {
        $payload = [
            'sensor_ids' => ['1']
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
        $expectedResult = '{"name":"Sensor Graph","description":"Displays a Sensor Graph of given sensors","parameters":{"sensor_ids":{"type":"multi_select","name":"Sensor ID","values":{"12":"sensor name"}},"from":{"type":"select","name":"From","values":{"3600":"Last Hour","86400":"Last Day","604800":"Last Week","2592000":"Last Month","-1":"All"}},"title":{"type":"text","name":"Name"}},"widgetId":"sensor_graph","width":6}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
