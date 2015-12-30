<?php

namespace Tests\Homie\Dashboard\Widgets;

use Homie\Dashboard\Widgets\SensorGraph;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

use Homie\Dashboard\Widgets\WidgetMetadataVo;
use Homie\Sensors\SensorGateway;

class SensorGraphWidgetTest extends TestCase
{

    /**
     * @var SensorGraph
     */
    private $subject;

    /**
     * @var SensorGateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->gateway = $this->getMock(SensorGateway::class);
        $this->subject = new SensorGraph($this->gateway);
    }

    public function testGetId()
    {
        $actualResult = $this->subject->getId();
        $this->assertEquals(SensorGraph::TYPE, $actualResult);
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

        $actual = json_encode($this->subject);
        $this->assertInternalType('string', $actual);
    }
}
