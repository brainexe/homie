<?php

namespace Homie\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class JobListener implements EventSubscriberInterface
{

    /**
     * @var SwitchInterface
     */
    private $controller;

    /**
     * @param SwitchInterface $controller
     * @Inject("@RadioController")
     * @todo fetch correct SwitchInterface
     */
    public function __construct(SwitchInterface $controller)
    {
        $this->controller = $controller;
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
        $this->controller->setStatus(
            $event->switch,
            $event->status
        );
    }
}
