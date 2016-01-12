<?php

namespace Homie\Webcam;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use BrainExe\InputControl\Event;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @InputControlAnnotation(name="webcam", tags={{"name"="expression_language"}})
 */
class InputControl implements InputControlInterface, ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            '/^webcam$/i' => 'takeShot',
            '/^webcam video (\d+) seconds$/i' => 'takeVideo'
        ];
    }

    public function takeShot()
    {
        $name  = microtime(true);
        $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

        $this->dispatchInBackground($event);
    }

    public function takeVideo(Event $event)
    {
        $name  = microtime(true);
        $event = new WebcamEvent($name, WebcamEvent::TAKE_VIDEO, $event->match);

        $this->dispatchInBackground($event);
    }

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('takePhoto', function () {
            throw new InvalidArgumentException('Function takePhoto() not available as condition');
        }, function () {
            unset($variables);
            $name  = microtime(true);
            $event = new WebcamEvent($name, WebcamEvent::TAKE_PHOTO);

            $this->dispatchInBackground($event);
        });
    }
}
