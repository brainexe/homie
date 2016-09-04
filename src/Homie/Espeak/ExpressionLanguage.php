<?php

namespace Homie\Espeak;

use BrainExe\Core\Traits\EventDispatcherTrait;
use Generator;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
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
        yield new Action('say', function (
            array $variables,
            string $text,
            int $volume = null,
            int $speed = null,
            string $speaker = null,
            int $devices = EspeakVO::ALL_DEVICES
        ) {
            unset($variables);
            $event = new EspeakEvent(new EspeakVO($text, $volume, $speed, $speaker, $devices));

            $this->dispatchInBackground($event);
        });

        yield new Action('sayInBrowser', function (array $variables, string $text, $speaker = null) {
            unset($variables);
            $event = new EspeakEvent(new EspeakVO($text, null, null, $speaker, EspeakVO::DEVICE_BROWSER));

            $this->dispatchInBackground($event);
        });
    }
}
