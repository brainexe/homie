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

        $actual_result = $this->subject->buildFromArray($array);

        $expected_result = new SensorVO();

        $expected_result->id = $id;
        $expected_result->name = $name;
        $expected_result->description = $description;
        $expected_result->interval = $interval;
        $expected_result->node = $node;
        $expected_result->pin = $pin;
        $expected_result->type = $type;
        $expected_result->last_value = $last_value;
        $expected_result->last_value_timestamp = $last_value_timestamp;

        $this->assertEquals($expected_result, $actual_result);
    }
}
