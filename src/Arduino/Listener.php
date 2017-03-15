<?php

namespace Homie\Arduino;

use BrainExe\Core\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;

/**
 * @EventListener
 */
class Listener
{

    /**
     * @var Device
     */
    private $serial;

    /**
     * @todo inject correct
     * @Inject("@Arduino.Device.Redis")
     * @param Device $serial
     */
    public function __construct(Device $serial)
    {
        $this->serial = $serial;
    }

    /**
     * @Listen(SerialEvent::SERIAL)
     * @param SerialEvent $event
     */
    public function handleEvent(SerialEvent $event)
    {
        $this->serial->sendSerial($event);
    }
}
