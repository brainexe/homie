<?php

namespace Tests\Homie\Sensors;

use Homie\Sensors\Chart;
use PHPUnit_Framework_TestCase as TestCase;

class ChartTest extends TestCase
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
                'sensorId' => $sensorId = 1212,
                'name' => $sensorName = 'name',
                'description' => $sensorDescription = 'description',
                'pin' => $sensorPin = 'pin',
                'type' => 'mockType',
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
                'color' => '#a01610',
                'type' => 'mockType',
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

    public function testFormatEmpty()
    {
        $actualResult = $this->subject->formatJsonData([], []);

        $expectedResult = [];

        $this->assertEquals($expectedResult, $actualResult);
    }
}
