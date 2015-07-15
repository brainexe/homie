<?php

namespace Homie\Sensors\GetValue;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Homie\Sensors\SensorVO;

class Event extends AbstractEvent
{

    const NAME = 'sensor.getValue';

    /**
     * @var SensorVO
     */
    private $sensorVO;

    /**
     * @param SensorVO $sensorVO
     */
    public function __construct(SensorVO $sensorVO)
    {
        parent::__construct(self::NAME);

        $this->sensorVO = $sensorVO;
    }

    /**
     * @return SensorVO
     */
    public function getSensorVO()
    {
        return $this->sensorVO;
    }
}
