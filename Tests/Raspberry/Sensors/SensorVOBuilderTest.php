<?php

namespace Tests\Raspberry\Sensors\SensorGateway;

use PHPUnit_Framework_TestCase;
use Raspberry\Sensors\SensorVO;
use Raspberry\Sensors\SensorVOBuilder;

/**
 * @Covers Raspberry\Sensors\SensorVOBuilder
 */
class SensorVOBuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SensorVOBuilder
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new SensorVOBuilder();
    }

    public function testBuildSensorVOFromArray()
    {
        $array = [
            'id' => $id = 'id',
            'name' => $name = 'name',
            'description' => $description = 'description',
            'interval' => $interval = 'interval',
            'node' => $node = 'node',
            'pin' => $pin = 'pin',
            'type' => $type = 'type',
            'last_value' => $last_value = 'last_value',
            'last_value_timestamp' => $last_value_timestamp = 'last_value_timestamp'
        ];

        $actualResult = $this->subject->buildFromArray($array);

        $expectedResult = new SensorVO();

        $expectedResult->sensorId = $id;
        $expectedResult->name = $name;
        $expectedResult->description = $description;
        $expectedResult->interval = $interval;
        $expectedResult->node = $node;
        $expectedResult->pin = $pin;
        $expectedResult->type = $type;
        $expectedResult->lastValue = $last_value;
        $expectedResult->lastValueTimestamp = $last_value_timestamp;

        $this->assertEquals($expectedResult, $actualResult);
    }
}
