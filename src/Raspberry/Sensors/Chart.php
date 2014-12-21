<?php

namespace Raspberry\Sensors;

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
            $sensorId = $sensor['id'];

            if (empty($sensorValues[$sensorId])) {
                continue;
            }

            $sensorJson = [
                'sensor_id' => $sensorId,
                'color' => $this->getColor($sensorId),
                'name' => $sensor['name'],
                'description' => $sensor['description'],
                'pin' => $sensor['pin'],
                'data' => []
            ];

            foreach ($sensorValues[$sensorId] as $timestamp => $value) {
                $sensorJson['data'][] = ['x' => (int)$timestamp, 'y' => (double)$value];
            }

            $output[] = $sensorJson;
        }

        return $output;
    }

    /**
     * @param integer $sensorId
     * @return string
     */
    private function getColor($sensorId)
    {
        return sprintf('#%s', substr(md5($sensorId), 0, 6));
    }
}
