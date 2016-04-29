<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;

/**
 * @Service("Sensors.DeleteOldValues", public=false)
 */
class DeleteOldValues
{

    const FRAMES = [
        3  * 86400 => 30 * 60,  // after 3 days, just keep one entry each 30 minutes
        14 * 86400 => 3 * 3600, // after 2 weeks, just keep one entry each 3 hours
    ];

    use RedisTrait;
    use TimeTrait;

    /**
     * @param int $sensorId
     * @return int $deleted_rows
     */
    public function deleteValues(int $sensorId) : int
    {
        $now     = $this->now();
        $deleted = 0;

        foreach (self::FRAMES as $since => $threshHold) {
            $untilTimestamp = $now - $since;
            $deleted += $this->deleteValuesForSensor($sensorId, $untilTimestamp, $threshHold);
        }

        return $deleted;
    }

    /**
     * @param int $sensorId
     * @param int $untilTimestamp
     * @param int $threshHold
     * @return int
     */
    private function deleteValuesForSensor(int $sensorId, int $untilTimestamp, int $threshHold) : int
    {
        $deleted        = 0;
        $key            = $this->getKey($sensorId);
        $oldValues      = $this->getRedis()->zrangebyscore($key, 0, $untilTimestamp, ['withscores' => true]);
        $lastTimestamp  = 0;

        foreach ($oldValues as $score => $timestamp) {
            if ($lastTimestamp + $threshHold > $timestamp) {
                $this->getRedis()->zrem($key, $score);

                $deleted += 1;
                continue;
            }

            $lastTimestamp = $timestamp;
        }

        return $deleted;
    }

    /**
     * @param int $sensorId
     * @return string
     */
    private function getKey(int $sensorId) : string
    {
        return sprintf(SensorValuesGateway::REDIS_SENSOR_VALUES, $sensorId);
    }
}
