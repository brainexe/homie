<?php

namespace Tests\Homie\Sensors;

use PHPUnit_Framework_TestCase;
use Homie\Sensors\SensorVO;
use Homie\Sensors\Builder;

/**
 * @covers Homie\Sensors\Builder
 */
class BuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Builder
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Builder();
    }

    public function testBuildSensorVOFromArray()
    {
        $array = [
            'sensorId' => $sensorId = 'id',
            'name' => $name = 'name',
            'description' => $description = 'description',
            'interval' => $interval = 'interval',
            'node' => $node = 'node',
            'pin' => $pin = 'pin',
            'type' => $type = 'type',
            'lastValue' => $lastValue = 'lastValue',
            'lastValueTimestamp' => $lastValueTimestamp = 'lastValueTimestamp'
        ];

        $actualResult = $this->subject->buildFromArray($array);

        $expectedResult = new SensorVO();

        $expectedResult->sensorId = $sensorId;
        $expectedResult->name = $name;
        $expectedResult->description = $description;
        $expectedResult->interval = $interval;
        $expectedResult->node = $node;
        $expectedResult->pin = $pin;
        $expectedResult->type = $type;
        $expectedResult->lastValue = $lastValue;
        $expectedResult->lastValueTimestamp = $lastValueTimestamp;

        $this->assertEquals($expectedResult, $actualResult);
    }
}
