<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use BrainExe\Core\Traits\TimeTrait;
use Iterator;
use Predis\Pipeline\Pipeline;

/**
 * @Service(public=false)
 */
class SensorValuesGateway
{

    const REDIS_SENSOR_VALUES = 'sensor_values:%d';

    use RedisTrait;
    use TimeTrait;
    use IdGeneratorTrait;

    /**
     * @param SensorVO $sensor
     * @param float $value
     */
    public function addValue(SensorVO $sensor, float $value)
    {
        $now = $this->now();
        $sensor->lastValue = $value;
        $sensor->lastValueTimestamp = $now;

        $redis = $this->getRedis()->pipeline();
        $key   = $this->getKey($sensor->sensorId);
        $id    = $this->generateUniqueId('sensorvalue:' . $sensor->sensorId);

        $redis->zadd(
            $key,
            [$id . '-' . $value => $now]
        );
        $redis->hmset(SensorGateway::REDIS_SENSOR_PREFIX . $sensor->sensorId, [
            'lastValue'          => $sensor->lastValue,
            'lastValueTimestamp' => $sensor->lastValueTimestamp
        ]);

        $redis->execute();
    }

    /**
     * @param array $sensorIds
     * @param int $timestamp
     * @return Iterator|float[]
     */
    public function getByTime(array $sensorIds, int $timestamp) : Iterator
    {
        $values = $this->getRedis()->pipeline(function (Pipeline $pipe) use ($sensorIds, $timestamp) {
            foreach ($sensorIds as $sensorId) {
                $key = $this->getKey($sensorId);

                $pipe->zrevrangebyscore($key, $timestamp, 0, ['limit' => [0, 1]]);
            }
        });

        foreach ($sensorIds as $index => $sensorId) {
            yield $sensorId => (float)explode('-', $values[$index][0], 2)[1];
        }
    }

    /**
     * @param int $sensorId
     * @param int $from
     * @param int $to
     * @return float[]
     */
    public function getSensorValues(int $sensorId, int $from, int $to) : array
    {
        $key         = $this->getKey($sensorId);
        $redisResult = $this->getRedis()->zrangebyscore($key, $from, $to, ['withscores' => true]);
        $result      = [];

        foreach ($redisResult as $part => $timestamp) {
            list(, $value) = explode('-', $part, 2);
            $result[$timestamp] = (float)$value;
        }

        return $result;
    }

    /**
     * @param int $sensorId
     * @return string
     */
    private function getKey(int $sensorId) : string
    {
        return sprintf(self::REDIS_SENSOR_VALUES, $sensorId);
    }
}
