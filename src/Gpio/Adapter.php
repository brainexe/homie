<?php

namespace Homie\Gpio;

abstract class Adapter
{

    /**
     * @return PinsCollection
     */
    abstract public function loadPins() : PinsCollection;

    /**
     * @param Pin $pin Pin
     */
    abstract public function updatePin(Pin $pin);

    /**
     * @param string $pin
     * @return Pin
     */
    public function loadPin($pin) : Pin
    {
        $pins = $this->loadPins();

        return $pins->getByPhysicalId($pin);
    }
}
