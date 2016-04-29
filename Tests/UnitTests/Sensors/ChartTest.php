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
                'sensorId' => $sensorId1 = 1212,
                'name' => $sensorName = 'name',
                'description' => $sensorDescription = 'description',
                'parameter' => 'parameter',
                'color' => 'colorful',
                'formatter' => 'mockFormatter',
            ],
            [
                'sensorId' => $sensorId2 = 1213,
                'name' => 'name2',
                'description' => 'description2',
                'parameter' => 'parameter2',
                'color' => 'colorful',
                'formatter' => 'mockFormatter',
            ],
            [
                'sensorId' => 'sensor_id_2',
            ]
        ];

        $sensorValues = [
            $sensorId1 => [
                $timestamp = 1212 => $sensorValue = 1200
            ],
            $sensorId2 => [
                1 => 2
            ]
        ];

        $actual = $this->subject->formatJsonData($sensors, $sensorValues);

        $expected = [
            $sensorId1 => [
                'sensorId' => $sensorId1,
                'name' => $sensorName,
                'description' => $sensorDescription,
                'color' => 'colorful',
                'formatter' => 'mockFormatter',
                'data' => [$timestamp, $sensorValue]
            ],
            $sensorId2 => [
                'sensorId' => $sensorId2,
                'name' => 'name2',
                'description' => 'description2',
                'color' => 'colorful',
                'formatter' => 'mockFormatter',
                'data' => [1, 2]
            ]
        ];

        $this->assertEquals($expected, iterator_to_array($actual));
    }

    public function testFormatEmpty()
    {
        $actual = $this->subject->formatJsonData([], []);

        $expected = [];

        $this->assertEquals($expected, iterator_to_array($actual));
    }
}
