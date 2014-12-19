<?php

namespace Raspberry\Tests\Radio;

use Raspberry\Sensors\Chart;

class ChartTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Chart
     */
    private $_subject;

    public function setUp()
    {
        $this->_subject = new Chart();
    }

    public function testFormatJsonData()
    {
        $sensors = [
        [
        'id' => $sensor_id = 'sensor_id',
        'name' => $sensor_name = 'name',
        'description' => $sensor_description = 'description',
        'pin' => $sensor_pin = 'pin',
        ],
        [
        'id' => $sensor_id_2 = 'sensor_id_2',
        ]
        ];

        $sensor_values = [
        $sensor_id => [
        $timestamp = 1212 => $sensor_value = 1200
        ]
        ];

        $actual_result = $this->_subject->formatJsonData($sensors, $sensor_values);

        $expected_result = [
        [
        'sensor_id' => $sensor_id,
        'name' => $sensor_name,
        'description' => $sensor_description,
        'color' => '#d96d86',
        'pin' => $sensor_pin,
        'data' => [
        [
         'x' => $timestamp,
         'y' => $sensor_value
        ]
        ]
        ]
        ];

        $this->assertEquals($expected_result, $actual_result);
    }
}
