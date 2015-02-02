<?php

namespace Raspberry\Tests\Radio;

use Raspberry\Sensors\Chart;

class ChartTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Chart
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Chart();
    }

    public function testFormatJsonData()
    {
        $sensors = [
        [
            'id' => $sensorId = 'sensor_id',
            'name' => $sensor_name = 'name',
            'description' => $sensor_description = 'description',
            'pin' => $sensor_pin = 'pin',
        ],
        [
            'id' => $sensorId_2 = 'sensor_id_2',
        ]
        ];

        $sensorValues = [
        $sensorId => [
        $timestamp = 1212 => $sensorValue = 1200
        ]
        ];

        $actualResult = $this->subject->formatJsonData($sensors, $sensorValues);

        $expectedResult = [
        [
            'sensor_id' => $sensorId,
            'name' => $sensor_name,
            'description' => $sensor_description,
            'color' => '#d96d86',
            'pin' => $sensor_pin,
            'data' => [
        [
             'x' => $timestamp,
             'y' => $sensorValue
        ]
        ]
        ]
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }
}
