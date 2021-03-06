<?php

namespace Homie\Sensors;

use BrainExe\Core\Annotations\Service;
use Iterator;

/**
 * @Service
 */
class Chart
{

    public const DEFAULT_TIME = 86400;

    /**
     * @param array[] $sensors
     * @param array[] $sensorValues
     * @return Iterator
     */
    public function formatJsonData(array $sensors, array $sensorValues) : Iterator
    {
        foreach ($sensors as $sensor) {
            $sensorId = $sensor['sensorId'];
            if (!empty($sensorValues[$sensorId])) {
                $sensorJson = [
                    'sensorId'    => (int)$sensorId,
                    'color'       => $sensor['color'],
                    'name'        => $sensor['name'],
                    'formatter'   => $sensor['formatter'],
                    'data'        => [] // will be filled with x/y values
                ];

                foreach ($sensorValues[$sensorId] as $timestamp => $value) {
                    $sensorJson['data'][] = (int)$timestamp;
                    $sensorJson['data'][] = (double)$value;
                }
                yield $sensorId => $sensorJson;
            }
        }
    }

    /**
     * @return array
     */
    public static function getTimeSpans() : array
    {
        return [
            3600        => _('Last hour'),
            10800       => _('Last 3 hours'),
            86400       => _('Last day'),
            86400 * 3   => _('Last 3 days'),
            86400 * 7   => _('Last week'),
            86400 * 30  => _('Last month'),
            -1          => _('All time'),
        ];
    }
}
