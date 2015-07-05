<?php

namespace Homie\Webcam;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Annotations\EventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class WebcamListener implements EventSubscriberInterface
{

    /**
     * @var Recorder
     */
    private $recorder;

    /**
     * @Inject("@Webcam.Recorder")
     * @param Recorder $recorder
     */
    public function __construct(Recorder $recorder)
    {
        $this->recorder = $recorder;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            WebcamEvent::TAKE_PHOTO => 'handlePictureEvent',
            WebcamEvent::TAKE_VIDEO => 'handleVideoEvent',
            WebcamEvent::TAKE_SOUND => 'handleSoundEvent',
        ];
    }

    /**
     * @param WebcamEvent $event
     */
    public function handlePictureEvent(WebcamEvent $event)
    {
        $this->recorder->takePhoto($event->name);
    }

    /**
     * @param WebcamEvent $event
     */
    public function handleVideoEvent(WebcamEvent $event)
    {
        $this->recorder->takeVideo($event->name, $event->duration);
    }

    /**
     * @param WebcamEvent $event
     */
    public function handleSoundEvent(WebcamEvent $event)
    {
        $this->recorder->takeSound($event->name, $event->duration);
    }
}
