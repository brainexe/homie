<?php

namespace Homie\Remote;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\Remote\Event\ReceivedEvent;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation("Remote.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @return ExpressionFunction[]|Generator
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('isRemoteCode', function (string $code) {
            return sprintf('($eventName == \'%s\' && $event->getCode() === %s)', ReceivedEvent::RECEIVED, $code);
        }, function (array $parameters, string $code) {
            if ($parameters['eventName'] !== ReceivedEvent::RECEIVED) {
                return false;
            }

            return $parameters['event']->getCode() === $code;
        });
    }
}
