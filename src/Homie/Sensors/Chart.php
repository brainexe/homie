<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;

/**
 * @Service(public=false)
 */
class Chart
{

    const DEFAULT_TIME = 86400;

    /**
     * @param array[] $sensors
     * @param array[] $sensorValues
     * @return array
     */
    public function formatJsonData(array $sensors, array $sensorValues)
    {
        $output = [];

        foreach ($sensors as $sensor) {
            $sensorId = $sensor['sensorId'];

            if (empty($sensorValues[$sensorId])) {
                continue;
            }

            $sensorJson = [
                'sensor_id'   => (int)$sensorId,
                'type'        => $sensor['type'],
                'color'       => $this->getColor($sensorId),
                'name'        => $sensor['name'],
                'description' => $sensor['description'],
                'data'        => []
            ];

            foreach ($sensorValues[$sensorId] as $timestamp => $value) {
                $sensorJson['data'][] = [
                    'x' => (int)$timestamp,
                    'y' => (double)$value
                ];
            }

            $output[] = $sensorJson;
        }

        return $output;
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

    /**
     * @param int $sensorId
     * @return string
     */
    private function getColor($sensorId)
    {
        // todo add color to sensor
        return sprintf('#%s', substr(md5($sensorId), 0, 6));
    }
}
