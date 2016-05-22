<?php

namespace Homie\VoiceControl;

use Homie\Expression\Entity;
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
            return sprintf('($eventName == \'%s\' && preg_match(%s, $event->getText(), $entity->payload[\'voice\']))', VoiceEvent::SPEECH, $pattern);
        }, function (array $variables, int $index = 0) {
            /** @var Entity $entity */
            $entity = $variables['entity'];

            return (string)$entity->payload['voice'][$index];
        });

        return [$voice];
    }
}
