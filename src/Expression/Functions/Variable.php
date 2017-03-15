<?php

namespace Homie\Expression\Functions;

use Generator;
use Homie\Expression\Annotation\ExpressionLanguage as ExpressionLanguageAnnotation;
use Homie\Expression\Variable as VariableModel;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @ExpressionLanguageAnnotation
 */
class Variable implements ExpressionFunctionProviderInterface
{

    /**
     * @var VariableModel
     */
    private $variable;

    /**
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
        yield new ExpressionFunction('getVariable', function (string $key) {
            return sprintf(
                '($container->get("Expression.Controller.Variables"))->getVariable("%s")',
                $key
            );
        }, function (array $parameters, string $property) {
            unset($parameters);
            return $this->variable->getVariable($property);
        });

        yield new ExpressionFunction('setVariable', function (string $name, string $value) {
            return sprintf(
                '($container->get("Expression.Controller.Variables"))->setVariable("%s", "%s")',
                $name,
                $value
            );
        }, function (array $parameters, string $key, string $value) {
            unset($parameters);
            $this->variable->setVariable($key, $value);
        });

        yield new ExpressionFunction('increaseVariable', function (string $name, $value = 1) {
            return sprintf(
                '($container->get("Expression.Controller.Variables"))->increaseVariable("%s", "%s")',
                $name,
                $value
            );
        }, function (array $parameters, string $key, $value = 1) {
            unset($parameters);
            $this->variable->increaseVariable($key, $value);
        });
    }
}
