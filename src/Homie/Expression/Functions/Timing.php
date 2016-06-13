<?php

namespace Homie\Expression\Functions;

use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use Generator;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Expression.Functions.Timing")
 */
class Timing implements ExpressionFunctionProviderInterface
{

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('isTiming', function (string $timingId) {
            return sprintf(
                '($eventName == \'%s\' && $event->getTimingId() === %s)',
                TimingEvent::TIMING_EVENT,
                $timingId
            );
        }, function (array $parameters, string $isTiming) {
            if ($parameters['eventName'] !== TimingEvent::TIMING_EVENT) {
                return false;
            }

            return $parameters['event']->getTimingId() === $isTiming;
        });
    }
}
