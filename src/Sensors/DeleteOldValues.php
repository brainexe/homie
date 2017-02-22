<?php

namespace Homie\Sensors;

use BrainExe\Core\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;

/**
 * @Service("Sensors.DeleteOldValues")
 */
class DeleteOldValues
{

    const FRAMES = [
        7  * 86400 => 30 * 60,  // after 7 days, just keep one entry each 30 minutes
        30 * 86400 => 2 * 3600, // after 4 weeks, just keep one entry each 2 hours
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
