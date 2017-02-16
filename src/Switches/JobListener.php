<?php

namespace Homie\Switches;


use BrainExe\Core\Annotations\EventListener;
use BrainExe\Core\Annotations\Listen;
use Homie\Switches\Change\Change;

/**
 * @EventListener
 */
class JobListener
{

    /**
     * @var Change
     */
    private $change;

    /**
     * @param Change $controller
     */
    public function __construct(Change $controller)
    {
        $this->change = $controller;
    }

    /**
     * @Listen(SwitchChangeEvent::CHANGE)
     * @param SwitchChangeEvent $event
     */
    public function handleChangeEvent(SwitchChangeEvent $event)
    {
        $this->change->setStatus(
            $event->getSwitch(),
            $event->getStatus()
        );
    }
}
