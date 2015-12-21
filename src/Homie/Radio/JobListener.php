<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Homie\Radio\Change\Change;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class JobListener implements EventSubscriberInterface
{

    /**
     * @var Change
     */
    private $change;

    /**
     * @param Change $controller
     * @Inject({
     *     "@Switches.Change.Change"
     * })
     */
    public function __construct(Change $controller)
    {
        $this->change = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SwitchChangeEvent::CHANGE_RADIO => 'handleChangeEvent'
        ];
    }

    /**
     * @param SwitchChangeEvent $event
     */
    public function handleChangeEvent(SwitchChangeEvent $event)
    {
        $this->change->setStatus(
            $event->switch,
            $event->status
        );
    }
}
