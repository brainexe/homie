<?php

namespace Homie\Sensors\Sensors\Temperature;

trait TemperatureTrait
{
    /**
     * @param float $temperature
     * @return bool
     */
    public function validateTemperature(float $temperature) : bool
    {
        if ($temperature < -40 || $temperature > 200) {
            return false;
        }

        return true;
    }
}
