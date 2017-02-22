<?php

namespace Homie\IFTTT;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Homie\IFTTT\Event\TriggerEvent;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions()
    {
        $trigger = new Action('triggerIFTTT', function (array $variables, string $eventName, string $value1 = null, string $value2 = null, string $value3 = null) {
            unset($variables);
            $event = new TriggerEvent($eventName, $value1, $value2, $value3);

            $this->dispatcher->dispatchEvent($event);
        });

        return [$trigger];
    }
}
