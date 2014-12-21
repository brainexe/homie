<?php

namespace Tests\Raspberry\Dashboard\Widgets\SensorWidget;

use PHPUnit_Framework_TestCase;
use Raspberry\Dashboard\Widgets\SensorWidget;
use Raspberry\Dashboard\Widgets\WidgetMetadataVo;

class SensorWidgetTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SensorWidget
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new SensorWidget();
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
        $actualResult = $this->subject->getMetadata();

        $this->assertInstanceOf(WidgetMetadataVO::class, $actualResult);
    }

    public function testJsonEncode()
    {
        $actualResult = json_encode($this->subject);

        $expectedResult = '{"name":"Sensor","parameters":{"sensor_id":"Sensor ID"},"widgetId":"sensor"}';
        $this->assertEquals($expectedResult, $actualResult);
    }
}
