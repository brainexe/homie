<?php

namespace Homie\IFTTT;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use Homie\IFTTT\Event\TriggerEvent;

/**
 * @EventListener("IFTTT.Listener")
 */
class Listener
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
     * @Listen(TriggerEvent::TRIGGER)
     * @param TriggerEvent $event
     */
    public function callTrigger(TriggerEvent $event)
    {
        $this->trigger->trigger($event->event);
    }
}
