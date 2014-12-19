<?php

namespace Raspberry\Radio;

use BrainExe\Core\Redis\Redis;
use BrainExe\Core\Traits\IdGeneratorTrait;
use BrainExe\Core\Traits\RedisTrait;
use Raspberry\Radio\VO\RadioVO;

/**
 * @Service(public=false)
 */
class RadioGateway
{

    use RedisTrait;
    use IdGeneratorTrait;

    const REDIS_RADIO = 'radios:%d';
    const REDIS_RADIO_IDS = 'radio_ids';

    /**
     * @return array[]
     */
    public function getRadios()
    {
        $radio_ids = $this->getRadioIds();

        /** @var Redis $pipeline */
        $pipeline = $this->getRedis()->multi(Redis::PIPELINE);

        foreach ($radio_ids as $radio_id) {
            $pipeline->HGETALL(self::getRadioKey($radio_id));
        }

        return $pipeline->exec();
    }

    /**
     * @param integer $radio_id
     * @return array
     */
    public function getRadio($radio_id)
    {
        return $this->getRedis()->HGETALL($this->getRadioKey($radio_id));
    }

    /**
     * @return integer[]
     */
    public function getRadioIds()
    {
        $radio_ids = $this->getRedis()->SMEMBERS(self::REDIS_RADIO_IDS);

        sort($radio_ids);

        return $radio_ids;
    }

    /**
     * @param RadioVO $radio_vo
     * @return integer $radio_id
     */
    public function addRadio(RadioVO $radio_vo)
    {
        $new_radio_id = $this->generateRandomId();

        $pipeline = $this->getRedis()->multi(Redis::PIPELINE);

        $key = $this->getRadioKey($new_radio_id);
        $pipeline->HMSET($key, [
        'id' => $new_radio_id,
        'name' => $radio_vo->name,
        'description' => $radio_vo->description,
        'pin' => $radio_vo->pin,
        'code' => $radio_vo->code,
        ]);

        $this->getRedis()->SADD(self::REDIS_RADIO_IDS, $new_radio_id);

        $pipeline->exec();

        $radio_vo->id = $new_radio_id;

        return $new_radio_id;
    }

    /**
     * @param RadioVO $radio_vo
     */
    public function editRadio(RadioVO $radio_vo)
    {
        $key = $this->getRadioKey($radio_vo->id);

        $redis = $this->getRedis();

        $redis->hMset($key, (array)$radio_vo);
    }

    /**
     * @param integer $radio_id
     */
    public function deleteRadio($radio_id)
    {
        $redis = $this->getRedis();

        $redis->SREM(self::REDIS_RADIO_IDS, $radio_id);
        $redis->DEL(self::getRadioKey($radio_id));
    }

    /**
     * @param integer $radio_id
     * @return string
     */
    private function getRadioKey($radio_id)
    {
        return sprintf(self::REDIS_RADIO, $radio_id);
    }
}
