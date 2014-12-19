<?php

namespace Raspberry\Espeak;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @InputControl(name="espeak")
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
        '/^(say|speak) (.*)$/' => 'say'
        ];
    }

    /**
     * @param Event $event
     */
    public function say(Event $event)
    {
        $event = new EspeakEvent(new EspeakVO($event->match));

        $this->dispatchEvent($event);
    }
}
