<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("SensorGateway")
 */
class SensorGateway
{

    const REDIS_SENSOR_PREFIX = 'sensor:';
    const SENSOR_IDS          = 'sensor_ids';

    use RedisTrait;
    use IdGeneratorTrait;

    /**
     * @param int[] $activeSensorIds
     * @return array[]
     */
    public function getSensors(array $activeSensorIds = []) : array
    {
        $sensorIds = $activeSensorIds ?: $this->getSensorIds();

        $redis = $this->getRedis()->pipeline();
        foreach ($sensorIds as $sensorId) {
            $redis->hgetall($this->getKey($sensorId));
        }

        return array_filter($redis->execute());
    }

    /**
     * @param int $node
     * @return array[]
     */
    public function getSensorsForNode(int $node) : array
    {
        $sensors = $this->getSensors();

        return array_filter($sensors, function (array $sensor) use ($node) {
            return $sensor['node'] == $node;
        });
    }

    /**
     * @return int[]
     */
    public function getSensorIds() : array
    {
        $sensorIds = $this->getRedis()->smembers(self::SENSOR_IDS);

        sort($sensorIds);

        return $sensorIds;
    }

    /**
     * @param SensorVO $sensorVo
     * @return int
     */
    public function addSensor(SensorVO $sensorVo) : int
    {
        $redis = $this->getRedis()->pipeline();
        $newId = $this->generateUniqueId('sensorid');

        $key = $this->getKey($newId);

        $sensorVo->sensorId           = $newId;
        $sensorVo->lastValue          = 0.0;
        $sensorVo->lastValueTimestamp = 0;

        $data = (array)$sensorVo;
        $data['tags'] = implode(',', $sensorVo->tags);
        $redis->hmset($key, $data);
        $redis->sadd(self::SENSOR_IDS, [$newId]);

        $redis->execute();

        $sensorVo->sensorId = $newId;

        return $newId;
    }

    /**
     * @param int $sensorId
     * @return array
     */
    public function getSensor(int $sensorId) : array
    {
        $key = $this->getKey($sensorId);

        return (array)$this->getRedis()->hgetall($key);
    }

    /**
     * @param SensorVO $sensorVO
     */
    public function save(SensorVO $sensorVO)
    {
        $key = $this->getKey($sensorVO->sensorId);

        $data = (array)$sensorVO;
        $data['tags'] = implode(',', $sensorVO->tags);
        $this->getRedis()->hmset($key, (array)$data);
    }

    /**
     * @param int $sensorId
     */
    public function deleteSensor(int $sensorId)
    {
        $redis = $this->getRedis();

        $redis->del($this->getKey($sensorId));
        $redis->srem(self::SENSOR_IDS, $sensorId);
        $redis->del(sprintf(SensorValuesGateway::REDIS_SENSOR_VALUES, $sensorId));
    }

    /**
     * @param int $sensorId
     * @return string
     */
    private function getKey(int $sensorId)
    {
        return self::REDIS_SENSOR_PREFIX . $sensorId;
    }
}
