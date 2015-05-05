<?php

namespace Homie\Sensors;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Event;
use Homie\Espeak\EspeakEvent;
use Homie\Espeak\EspeakVO;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;

/**
 * @InputControlAnnotation("InputControl.Sensor")
 */
class InputControl implements EventSubscriberInterface
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            '/^sensor say (\d+)$/i' => 'espeakSensor'
        ];
    }

    /**
     * @param Event $event
     */
    public function espeakSensor(Event $event)
    {
        $sensorId = $event->match;

        // todo fetch value
        $event = new EspeakEvent(new EspeakVO($sensorId));

        $this->dispatchEvent($event);
    }
}
