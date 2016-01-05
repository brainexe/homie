<?php

namespace Homie\Espeak;

use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\InputControl\Annotations\InputControl as InputControlAnnotation;
use BrainExe\InputControl\Annotations\InputControlInterface;
use BrainExe\InputControl\Event;
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

        $this->dispatchEvent($event);
    }

    /**
     * @return ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        $speak = new ExpressionFunction('say', function ($text) {
        }, function (array $variables, $text) {
            unset($variables);
            $event = new EspeakEvent(new EspeakVO($text));

            $this->dispatchEvent($event);
        });

        yield $speak;
    }
}
