<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Annotations\Annotations\Service;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
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

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @Inject({"@service_container"})
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->registerNativeFunctions();

        $this->container = $container;
    }

    /**
     * @param string $functionName
     * @param string $serviceId
     */
    public function lazyRegister(string $functionName, string $serviceId)
    {
        $this->lazyLoad[$serviceId] = true;

        $this->register($functionName, function (...$params) use ($serviceId, $functionName) {
            $this->ensureLoaded($serviceId);

            return $this->getFunctions()[$functionName]['compiler'](...$params);
        }, function (...$params) use ($functionName, $serviceId) {
            $this->ensureLoaded($serviceId);

            return $this->getFunctions()[$functionName]['evaluator'](...$params);
        });
    }

    public function loadAll()
    {
        foreach (array_keys($this->lazyLoad) as $serviceId) {
            $this->ensureLoaded($serviceId);
        }
    }

    /**
     * @param string $serviceId
     */
    private function ensureLoaded(string $serviceId)
    {
        if (isset($this->lazyLoad[$serviceId])) {
            /** @var ExpressionFunctionProviderInterface $provider */
            $provider = $this->container->get($serviceId);
            $this->registerProvider($provider);

            unset($this->lazyLoad[$serviceId]);
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
}
