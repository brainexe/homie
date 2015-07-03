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

    use RedisTrait;
    use TimeTrait;

    /**
     * @param integer $sensorId
     * @param double $value
     */
    public function addValue($sensorId, $value)
    {
        $redis = $this->getRedis()->pipeline();
        $now   = $this->now();
        $key   = $this->getKey($sensorId);

        $redis->ZADD($key, $now, $now . '-' . $value);

        $redis->HMSET(SensorGateway::REDIS_SENSOR_PREFIX . $sensorId, [
            'lastValue' => $value,
            'lastValueTimestamp' => $now
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

        if ($from) {
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

        $untilTimestamp = $this->now() - 86400;
        $key            = $this->getKey($sensorId);
        $oldValues      = $redis->ZRANGEBYSCORE($key, 0, $untilTimestamp, ['withscores' => true]);

        $deleted       = 0;
        $lastTimestamp = 0;
        foreach ($oldValues as $score => $timestamp) {
            if ($lastTimestamp + (60 * 30) > $timestamp) {
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
