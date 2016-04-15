<?php

namespace Homie\Sensors\Exception;

use Exception;
use Homie\Sensors\SensorVO;

class InvalidSensorValueException extends Exception
{
    /**
     * @var SensorVO
     */
    private $sensorVO;

    /**
     * @param SensorVO $sensorVO
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(
        SensorVO $sensorVO,
        string $message,
        int $code = null,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->sensorVO = $sensorVO;
    }

    /**
     * @return SensorVO
     */
    public function getSensor() : SensorVO
    {
        return $this->sensorVO;
    }
}
