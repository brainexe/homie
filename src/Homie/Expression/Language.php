<?php

namespace Homie\Expression;


use BrainExe\Annotations\Annotations\Service;

use Symfony\Component\ExpressionLanguage\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @Service("Expression.Language")
 */
class Language extends ExpressionLanguage
{
    /**
     * @var array
     */
    public $lazyLoad = [];

    public function __construct()
    {
        parent::__construct();

        $this->registerNativeFunctions();
    }

    /**
     * @param string $functionName
     * @param callable $functions
     */
    public function lazyRegister(string $functionName, callable $functions)
    {
        $this->register($functionName, function (...$params) use ($functions, $functionName) {
            return $this->getFunction($functionName, $functions)['compiler'](...$params);
        }, function (...$params) use ($functionName, $functions) {
            return $this->getFunction($functionName, $functions)['evaluator'](...$params);
        });
    }

    /**
     * @param string $functionName
     * @param callable $functions
     */
    private function getFunction(string $functionName, callable $functions)
    {
        $function = $this->functions[$functionName] ?? null;

        if (!$function) {
            // todo matze
           foreach ($functions() as $name => $function) {
               print_r([$name, $function]);
           }
        }
    }

    /**
     * @param string|Expression $expression
     * @param array $values
     * @return string
     */
    public function evaluate($expression, $values = array())
    {
        if (empty($expression)) {
            return '';
        }

        return parent::evaluate($expression, $values);
    }

    /**
     * @return array[]
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * @return string[]
     */
    public function getParameterNames()
    {
        return [
            'event',
            'eventName',
        ];
    }

    private function registerNativeFunctions()
    {
        $functions = [
            'sprintf',
            'date',
            'time',
            'microtime',
            'rand',
            'round',
            'sleep',
            'preg_match',
            'json_decode',
            'json_encode',
        ];

        foreach ($functions as $function) {
            $this->register($function, function (...$parameters) use ($function) {
                return sprintf(
                    '%s(%s)',
                    $function,
                    implode(', ', $parameters)
                );
            }, function (array $parameters, ...$params) use ($function) {
                unset($parameters);
                return $function(...$params);
            });
        }
    }

    public function loadAll()
    {
        return []; // todo matze
    }
}
