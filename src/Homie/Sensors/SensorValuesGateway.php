<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Redis\PhpRedis;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;

/**
 * @Service(public=false)
 */
class SensorValuesGateway
{

    const REDIS_SENSOR_VALUES = 'sensor_values:%d';

    const CLEAN_SINCE     = 172800; // start cleaning up values after 2 days
    const CLEAN_THRESHOLD = 240; // try to keep values each 15 minutes

    use RedisTrait;
    use TimeTrait;

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

        $redis->ZADD($key, $now, $now . '-' . $value);
        $redis->HMSET(SensorGateway::REDIS_SENSOR_PREFIX . $sensor->sensorId, [
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
        $redisResult = $this->getRedis()->ZRANGEBYSCORE($key, $from, $now);
        $result      = [];

        foreach ($redisResult as $part) {
            list($timestamp, $value) = explode('-', $part);
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
        $redis = $this->getRedis();

        $untilTimestamp = $this->now() - self::CLEAN_SINCE;
        $key            = $this->getKey($sensorId);
        $oldValues      = $redis->ZRANGEBYSCORE($key, 0, $untilTimestamp, ['withscores' => true]);
        $deleted        = 0;
        $lastTimestamp  = 0;

        foreach ($oldValues as $score => $timestamp) {
            if ($lastTimestamp + self::CLEAN_THRESHOLD > $timestamp) {
                $redis->ZREM($key, $score);

                $deleted += 1;
                continue;
            }

            $lastTimestamp = $timestamp;
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
