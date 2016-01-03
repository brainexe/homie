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
     * @var Action
     */
    private $action;

    /**
     * @Inject("@IFTTT.Action")
     * @param Action $action
     */
    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            IFTTTEvent::ACTION => 'callAction',
        ];
    }

    /**
     * @param IFTTTEvent $event
     */
    public function callAction(IFTTTEvent $event)
    {
        $this->action->trigger($event->event);
    }
}
