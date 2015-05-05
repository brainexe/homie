<?php

namespace Tests\Homie\Sensors;

use Homie\Sensors\Chart;

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
                'sensorId' => $sensorId = 'sensor_id',
                'name' => $sensorName = 'name',
                'description' => $sensorDescription = 'description',
                'pin' => $sensorPin = 'pin',
            ],
            [
                'sensorId' => 'sensor_id_2',
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
                'name' => $sensorName,
                'description' => $sensorDescription,
                'color' => '#d96d86',
                'pin' => $sensorPin,
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
