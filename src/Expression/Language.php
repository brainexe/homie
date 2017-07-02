<?php

namespace Homie\Expression;

use BrainExe\Core\Annotations\Service;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @Service
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
        $this->lazyLoad[$functionName] = $functions;

        $this->register($functionName, function (...$params) use ($functionName) {
            return $this->getFunction($functionName)['compiler'](...$params);
        }, function (...$params) use ($functionName) {
            return $this->getFunction($functionName)['evaluator'](...$params);
        });
    }

    /**
     * @inheritdoc
     */
    public function register($name, callable $compiler, callable $evaluator)
    {
        $this->functions[$name] = [
            'compiler'  => $compiler,
            'evaluator' => $evaluator
        ];
    }

    /**
     * @param string $functionName
     * @return array
     */
    private function getFunction(string $functionName): array
    {
        if (isset($this->lazyLoad[$functionName])) {
            $functions = $this->lazyLoad[$functionName];
            foreach ($functions() as $function) {
                /** @var ExpressionFunction $function */
                unset($this->lazyLoad[$function->getName()]);
                $this->addFunction($function);
            }
            unset($this->lazyLoad[$functionName]);
        }

        return $this->functions[$functionName];
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
    public function getFunctions() : array
    {
        return $this->functions;
    }

    /**
     * @return string[]
     */
    public function getParameterNames() : array
    {
        return [
            'event',
            'eventName',
        ];
    }

    private function registerNativeFunctions() : void
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
                return $function(...$params);
            });
        }
    }

    public function loadAll()
    {
        foreach (array_keys($this->lazyLoad) as $functionName) {
            $this->getFunction($functionName);
        }
    }
}
