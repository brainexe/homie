<?php

namespace Raspberry\Webcam;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class WebcamListener implements EventSubscriberInterface
{

    /**
     * @var Webcam
     */
    private $webcam;

    /**
     * @inject("@Webcam")
     * @param Webcam $webcam
     */
    public function __construct(Webcam $webcam)
    {
        $this->webcam = $webcam;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
        WebcamEvent::TAKE_PHOTO => 'handleWebcamEvent'
        ];
    }

    /**
     * @param WebcamEvent $event
     */
    public function handleWebcamEvent(WebcamEvent $event)
    {
        $this->webcam->takePhoto($event->name);
    }
}
