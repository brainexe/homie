<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;

/**
 * @Service(public=false)
 */
class SensorValuesGateway
{

    const REDIS_SENSOR_VALUES = 'sensor_values:%d';

    const FRAMES = [
        3 * 86400  => 30 * 60,  // after 3 days, just keep one entry each 30 minutes
        14 * 86400 => 3 * 3600, // after 2 weeks, just keep one entry each 3 hours
    ];

    use RedisTrait;
    use TimeTrait;
    use IdGeneratorTrait;

    /**
     * @param SensorVO $sensor
     * @param double $value
     */
    public function addValue(SensorVO $sensor, $value)
    {
        $now = $this->now();
        $sensor->lastValue = $value;
        $sensor->lastValueTimestamp = $now;

        $redis = $this->getRedis()->pipeline();
        $key   = $this->getKey($sensor->sensorId);
        $id    = $this->generateUniqueId('sensorvalue');

        $redis->zadd($key, $now, $id . '-' . $value);
        $redis->hmset(SensorGateway::REDIS_SENSOR_PREFIX . $sensor->sensorId, [
            'lastValue' => $sensor->lastValue,
            'lastValueTimestamp' => $sensor->lastValueTimestamp
        ]);

        $redis->execute();
    }

    /**
     * @param integer $sensorId
     * @param integer $from
     * @return array[]
     */
    public function getSensorValues($sensorId, $from)
    {
        $now = $this->now();

        if ($from === -1) {
            $from = 0;
        } elseif ($from) {
            $from = $now - $from;
        }

        $key         = $this->getKey($sensorId);
        $redisResult = $this->getRedis()->zrangebyscore($key, $from, $now, ['withscores' => true]);
        $result      = [];

        foreach ($redisResult as $part => $timestamp) {
            list(, $value) = explode('-', $part, 2);
            $result[$timestamp] = $value;
        }

        return $result;
    }

    /**
     * @param integer $sensorId
     * @return integer $deleted_rows
     */
    public function deleteOldValues($sensorId)
    {
        $redis   = $this->getRedis();
        $now     = $this->now();
        $deleted = 0;

        foreach (self::FRAMES as $since => $threshHold) {
            $untilTimestamp = $now - $since;
            $key            = $this->getKey($sensorId);
            $oldValues      = $redis->zrangebyscore($key, 0, $untilTimestamp, ['withscores' => true]);
            $lastTimestamp  = 0;

            foreach ($oldValues as $score => $timestamp) {
                if ($lastTimestamp + $threshHold > $timestamp) {
                    $redis->zrem($key, $score);

                    $deleted += 1;
                    continue;
                }

                $lastTimestamp = $timestamp;
            }
        }

        return $deleted;
    }

    /**
     * @param integer $sensorId
     * @return string
     */
    private function getKey($sensorId)
    {
        return sprintf(self::REDIS_SENSOR_VALUES, $sensorId);
    }
}
