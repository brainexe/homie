<?php

namespace Homie\Arduino;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("Arduino.Listener")
 */
class Listener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SerialEvent::SERIAL => 'handleEvent'
        ];
    }

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
     * @param SerialEvent $event
     */
    public function handleEvent(SerialEvent $event)
    {
        $this->serial->sendSerial($event);
    }
}
