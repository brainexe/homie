<?php

namespace Tests\Homie\Dashboard\Widgets;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\SensorWidget;
use Homie\Dashboard\Widgets\WidgetMetadataVo;
use Homie\Sensors\SensorGateway;

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

        $actual   = json_encode($this->subject);
        $expected = '{"name":"Sensor","description":"Displays the current value of a given sensor","parameters":{"title":{"name":"Title","type":"text","default":"Sensor"},"sensor_id":{"name":"Sensor","values":{"12":"sensor name"},"type":"single_select"},"from":{"type":"single_select","name":"From","values":{"3600":"Last hour","10800":"Last 3 hours","86400":"Last day","604800":"Last week","2592000":"Last month","-1":"All time"},"default":86400},"width":{"name":"Width","type":"number","min":1,"max":12,"default":4},"height":{"name":"Height","type":"number","min":1,"max":12,"default":3}},"widgetId":"sensor","width":4,"height":3}';

        $this->assertEquals($expected, $actual);
    }
}
