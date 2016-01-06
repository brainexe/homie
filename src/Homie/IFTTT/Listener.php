<?php

namespace Homie\IFTTT;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Homie\IFTTT\Event\TriggerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener("IFTTT.Listener")
 */
class Listener implements EventSubscriberInterface
{

    /**
     * @var Trigger
     */
    private $trigger;

    /**
     * @Inject("@IFTTT.Trigger")
     * @param Trigger $action
     */
    public function __construct(Trigger $action)
    {
        $this->trigger = $action;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            TriggerEvent::TRIGGER => 'callTrigger',
        ];
    }

    /**
     * @param TriggerEvent $event
     */
    public function callTrigger(TriggerEvent $event)
    {
        $this->trigger->trigger($event->event);
    }
}
