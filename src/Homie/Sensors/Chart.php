<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use Generator;

/**
 * @Service(public=false)
 */
class Chart
{

    const DEFAULT_TIME = 86400;

    /**
     * @param array[] $sensors
     * @param array[] $sensorValues
     * @return Generator
     */
    public function formatJsonData(array $sensors, array $sensorValues)
    {
        foreach ($sensors as $sensor) {
            $sensorId = $sensor['sensorId'];

            if (empty($sensorValues[$sensorId])) {
                continue;
            }

            $sensorJson = [
                'sensor_id'   => (int)$sensorId,
                'type'        => $sensor['type'],
                'color'       => $sensor['color'],
                'name'        => $sensor['name'],
                'description' => $sensor['description'],
                'formatter'   => $sensor['formatter'],
                'data'        => [] // will be filled with x/y values
            ];

            foreach ($sensorValues[$sensorId] as $timestamp => $value) {
                $sensorJson['data'][] = (int)$timestamp;
                $sensorJson['data'][] = (double)$value;
            }

            yield $sensorJson;
        }
    }

    /**
     * @return array
     */
    public static function getTimeSpans()
    {
        return [
            3600        => _('Last hour'),
            10800       => _('Last 3 hours'),
            86400       => _('Last day'),
            86400 * 7   => _('Last week'),
            86400 * 30  => _('Last month'),
            -1          => _('All time'),
        ];
    }
}
