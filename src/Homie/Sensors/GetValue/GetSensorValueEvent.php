<?php

namespace Homie\Sensors\GetValue;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Homie\Sensors\SensorVO;

class GetSensorValueEvent extends AbstractEvent
{

    const NAME = 'sensor.getValue';

    /**
     * @var SensorVO
     */
    private $sensorVO;

    /**
     * @param SensorVO $sensorVo
     */
    public function __construct(SensorVO $sensorVo)
    {
        parent::__construct(self::NAME);

        $this->sensorVO = $sensorVo;
    }

    /**
     * @return SensorVO
     */
    public function getSensorVO() : SensorVO
    {
        return $this->sensorVO;
    }
}
