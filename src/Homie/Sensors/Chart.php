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
                'sensor_id' => (int)$sensorId,
                'type'      => $sensor['type'],
                'color'     => $this->getColor($sensorId),
                'name'      => $sensor['name'],
                'description' => $sensor['description'],
                'data'      => []
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
     * @param int $sensorId
     * @return string
     */
    private function getColor($sensorId)
    {
        return sprintf('#%s', substr(md5($sensorId), 0, 6));
    }
}
