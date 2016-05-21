<?php

namespace Homie\VoiceControl;

use BrainExe\Core\Traits\LoggerTrait;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;

/**
 * @ExpressionLanguageAnnotation("VoiceControl.ExpressionLanguage")
 */
class ExpressionLanguage implements ExpressionFunctionProviderInterface
{

    /**
     * @return ExpressionFunction[]
     */
    public function getFunctions()
    {
        $voice = new ExpressionFunction('voice', function (string $pattern) {
            return sprintf('($eventName == \'%s\' && preg_match(%s, $event->getText()))', VoiceEvent::SPEECH, $pattern);
        }, function (array $variables, string $pattern) {
            if ($variables['eventName'] !== VoiceEvent::SPEECH) {
                return [];
            }

            preg_match($pattern, $variables['event']->getText(), $matches);

            return (array)$matches;
        });

        return [$voice];
    }
}
