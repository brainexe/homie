<?php

namespace Homie\Espeak;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use InvalidArgumentException;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Espeak.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    use EventDispatcherTrait;

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('say', function (string $text, int $volume = null, int $speed = null) {
            unset($text, $volume, $speed);
            throw new InvalidArgumentException('say() is not available in this context');
        }, function (array $variables, string $text, int $volume = null, int $speed = null) {
            unset($variables);
            $event = new EspeakEvent(new EspeakVO($text, $volume, $speed));

            $this->dispatchInBackground($event);
        });
    }
}
