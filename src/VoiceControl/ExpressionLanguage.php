<?php

namespace Homie\VoiceControl;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation("VoiceControl.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{
    /**
     * @var string[]
     */
    public static $currentMatch = [];

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions()
    {
        $voice = new ExpressionFunction('voice', function (string $pattern) {
            return sprintf('($eventName == \'%s\' && preg_match(%s, $event->getText(), %s::$currentMatch))', VoiceEvent::SPEECH, $pattern, self::class);
        }, function (array $variables, int $index = 0) {
            unset($variables);
            return (string)self::$currentMatch[$index];
        });

        return [$voice];
    }
}
