<?php

namespace Homie\Sensors;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
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
    public function getSensors(array $activeSensorIds = [])
    {
        $sensorIds = $activeSensorIds ?: $this->getSensorIds();

        $redis = $this->getRedis()->pipeline();
        foreach ($sensorIds as $sensorId) {
            $redis->HGETALL($this->getKey($sensorId));
        }

        return $redis->execute();
    }

    /**
     * @param integer $node
     * @return array[]
     */
    public function getSensorsForNode($node)
    {
        $sensors = $this->getSensors();

        return array_filter($sensors, function ($sensor) use ($node) {
            return $sensor['node'] == $node;
        });
    }

    /**
     * @return integer[]
     */
    public function getSensorIds()
    {
        $sensorIds = $this->getRedis()->sMembers(self::SENSOR_IDS);

        sort($sensorIds);

        return $sensorIds;
    }

    /**
     * @param SensorVO $sensorVo
     * @return integer
     */
    public function addSensor(SensorVO $sensorVo)
    {
        $redis = $this->getRedis()->pipeline();
        $newId = $this->generateUniqueId();

        $key = $this->getKey($newId);

        $sensorVo->sensorId           = $newId;
        $sensorVo->lastValue          = 0.0;
        $sensorVo->lastValueTimestamp = 0;

        $data = (array)$sensorVo;
        $data['tags'] = implode(',', $sensorVo->tags);
        $redis->HMSET($key, $data);
        $redis->sAdd(self::SENSOR_IDS, [$newId]);

        $redis->execute();

        $sensorVo->sensorId = $newId;

        return $newId;
    }

    /**
     * @param integer $sensorId
     * @return array
     */
    public function getSensor($sensorId)
    {
        $key = $this->getKey($sensorId);

        return $this->getRedis()->hGetAll($key);
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
     * @param integer $sensorId
     */
    public function deleteSensor($sensorId)
    {
        $redis = $this->getRedis();

        $redis->del($this->getKey($sensorId));
        $redis->sRem(self::SENSOR_IDS, $sensorId);
        $redis->del(sprintf(SensorValuesGateway::REDIS_SENSOR_VALUES, $sensorId));
    }

    /**
     * @param integer $sensorId
     * @return string
     */
    private function getKey($sensorId)
    {
        return self::REDIS_SENSOR_PREFIX . $sensorId;
    }
}
