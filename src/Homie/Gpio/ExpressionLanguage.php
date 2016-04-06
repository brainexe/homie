<?php

namespace Homie\Gpio;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation("Gpio.ExpressionLanguage")
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

            // todo implement GPIO
            $this->dispatchInBackground($event);
        });
    }
}
