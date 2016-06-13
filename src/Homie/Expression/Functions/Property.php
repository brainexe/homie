<?php

namespace Homie\Expression\Functions;

use Generator;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Homie\Expression\Entity;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Expression.Functions.Property")
 */
class Property implements ExpressionFunctionProviderInterface
{

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('setProperty', function (string $property, string $value) {
            return sprintf('($entity->payload[%s] = %s)', $property, $value);
        }, function (array $parameters, string $property, string $value) {
            /** @var Entity $entity */
            $entity                     = $parameters['entity'];
            $entity->payload[$property] = $value;
        });

        yield new ExpressionFunction('getProperty', function (string $property) {
            return sprintf('$entity->payload[%s]', $property);
        }, function (array $parameters, string $property) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];

            return $entity->payload[$property];
        });
    }
}
