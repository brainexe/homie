<?php

namespace Homie\Expression\Functions;

use Generator;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Homie\Expression\Entity;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Expression.Functions.Counter")
 */
class Counter implements ExpressionFunctionProviderInterface
{

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new Action('increaseCounter', function (array $parameters) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];
            if (empty($entity->payload['counter'])) {
                $entity->payload['counter'] = 1;
            } else {
                $entity->payload['counter']++;
            }
        });
    }
}
