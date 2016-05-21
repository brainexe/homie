<?php

namespace Homie\Gpio\Adapter;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Arduino\SerialEvent;
use Homie\Gpio\Adapter;
use Homie\Gpio\Pin;
use Homie\Gpio\PinsCollection;

/**
 * @Service("Gpio.Adapter.Arduino", public=false)
 */
class Arduino extends Adapter
{

    use EventDispatcherTrait;

    /**
     * @return PinsCollection
     */
    public function loadPins() : PinsCollection
    {
        $pins = new PinsCollection();

        // todo load?!

        return $pins;
    }

    /**
     * @param Pin $pin Pin
     */
    public function updatePin(Pin $pin)
    {
        $value = $pin->getValue();

        $event = new SerialEvent(SerialEvent::DIGITAL, $pin->getPhysicalId(), $value);

        $this->dispatchEvent($event);
    }
}
