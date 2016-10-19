<?php

namespace Homie\Sensors\Sensors\Humid;

trait HumidityTrait
{
    /**
     * @param float $humidity
     * @return bool
     */
    public function validateHumidity(float $humidity) : bool
    {
        if ($humidity < 0 || $humidity > 100) {
            return false;
        }

        return true;
    }
}
