<?php

namespace Homie\IFTTT;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
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
            IFTTTEvent::TRIGGER => 'callTrigger',
        ];
    }

    /**
     * @param IFTTTEvent $event
     */
    public function callTrigger(IFTTTEvent $event)
    {
        $this->trigger->trigger($event->event);
    }
}
