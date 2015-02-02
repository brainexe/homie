<?php

namespace Raspberry\Radio;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class RadioJobListener implements EventSubscriberInterface
{

    /**
     * @var RadioController
     */
    private $radioController;

    /**
     * @param RadioController $radioController
     * @Inject("@RadioController")
     */
    public function __construct(RadioController $radioController)
    {
        $this->radioController = $radioController;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
        RadioChangeEvent::CHANGE_RADIO => 'handleChangeEvent'
        ];
    }

    /**
     * @param RadioChangeEvent $event
     */
    public function handleChangeEvent(RadioChangeEvent $event)
    {
        $this->radioController->setStatus($event->radio_vo->code, $event->radio_vo->pin, $event->status);
    }
}
