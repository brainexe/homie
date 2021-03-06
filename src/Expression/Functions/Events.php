<?php

namespace Homie\Expression\Functions;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\FileCacheTrait;

use Generator;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use ReflectionClass;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation
 */
class Events implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;
    use FileCacheTrait;

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('isEvent', function (string $eventId) {
            return sprintf('($eventName === %s)', $eventId);
        }, function (array $parameters, string $eventId) {
            return $parameters['eventName'] === $eventId;
        });

        yield new Action('event', function (array $parameters, string $type, ...$eventArguments) {
            unset($parameters);
            $events = $this->includeFile('events');

            $reflection = new ReflectionClass($events[$type]['class']);
            /** @var AbstractEvent $event */
            $event = $reflection->newInstanceArgs($eventArguments);

            $this->dispatchEvent($event);
        });
    }
}
