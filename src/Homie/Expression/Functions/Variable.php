<?php

namespace Homie\Expression\Functions;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Core\Traits\RedisTrait;
use Generator;
use Homie\Expression\Action;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Homie\Expression\Entity;
use Homie\Expression\Variable as VariableModel;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation("Expression.Functions.Variable")
 */
class Variable implements ExpressionFunctionProviderInterface
{

    /**
     * @var VariableModel
     */
    private $variable;

    /**
     * @Inject({"@Expression.Variable"})
     * @param VariableModel $variable
     */
    public function __construct(VariableModel $variable)
    {
        $this->variable = $variable;
    }

    /**
     * @return Generator|ExpressionFunction[] An array of Function instances
     */
    public function getFunctions()
    {
        yield new ExpressionFunction('getVariable', function (string $name, string $value) {
            return sprintf('($this->get())', $name, $value);
        }, function (array $parameters, string $property, string $value) {
            /** @var Entity $entity */
            $entity                     = $parameters['entity'];
            $entity->payload[$property] = $value;
        });

        yield new Action('setVariable', function (array $parameters, $name, $value) {
            unset($parameters);
            // todo set variable
        });
    }
}
