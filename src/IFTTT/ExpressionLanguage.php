<?php

namespace Homie\IFTTT;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Homie\IFTTT\Event\TriggerEvent;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("IFTTT.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions()
    {
        $trigger = new Action('triggerIFTTT', function (array $variables, string $eventName, string $value1 = null, string $value2 = null, string $value3 = null) {
            unset($variables);
            $event = new TriggerEvent($eventName, $value1, $value2, $value3);

            $this->dispatchEvent($event);
        });

        return [$trigger];
    }
}
