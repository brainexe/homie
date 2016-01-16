<?php

namespace Homie\Gpio;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\IFTTT\Event\TriggerEvent;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @Service("Gpio.ExpressionLanguage", tags={{"name"="expression_language"}}, public=false)
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @return Generator|ExpressionFunction[]
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('setGPIOPin', function ($pin, $value) {
            unset($pin, $value);
            throw new InvalidArgumentException('triggerIFTTT() is not available in this context');
        }, function (array $variables, $pin, $value) {
            unset($variables);

            $this->dispatchInBackground($event);
        });
    }
}
