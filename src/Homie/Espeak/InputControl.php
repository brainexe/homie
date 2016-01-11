<?php

namespace Homie\Espeak;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @InputControlAnnotation("InputControl.espeak", tags={{"name"="expression_language"}})
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
            '/^(say|speak) (.*)$/' => 'say'
        ];
    }

    /**
     * @param Event $event
     */
    public function say(Event $event)
    {
        $event = new EspeakEvent(new EspeakVO($event->matches[1]));

        $this->dispatchInBackground($event);
    }

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('say', function ($text) {
            throw new InvalidArgumentException('say() is not available in this context');
        }, function (array $variables, $text, $volume = 100, $speed = 100) {
            unset($variables);
            $event = new EspeakEvent(new EspeakVO($text, $volume, $speed));

            $this->dispatchInBackground($event);
        });
    }
}
