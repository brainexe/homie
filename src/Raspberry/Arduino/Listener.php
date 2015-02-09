<?php

namespace Raspberry\Arduino;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("ArduinoListener")
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
     * @var Serial
     */
    private $serial;

    /**
     * @Inject("@Arduino.Serial")
     * @param Serial $serial
     */
    public function __construct(Serial $serial)
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
