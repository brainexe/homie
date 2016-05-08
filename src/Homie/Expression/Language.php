<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use Exception;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ParserCache\ParserCacheInterface;

/**
 * @todo avoid lazy service. Register providers lazy instead
 * @Service("Expression.Language", public=false, lazy=true)
 */
class Language extends ExpressionLanguage
{
    /**
     * {@inheritdoc}
     */
    public function __construct(ParserCacheInterface $cache = null, array $providers = [])
    {
        parent::__construct($cache, $providers);

        $this->registerNativeFunctions();
        $this->registerPropertyFunctions();
        $this->registerTiming();
        $this->registerCounter();
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
            'entity'
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
            'sleep'
        ];

        foreach ($functions as $function) {
            $this->register($function, function (...$parameters) use ($function) {
                return sprintf('%s(%s)', $function, implode(', ', $parameters));
            }, function (array $parameters, ...$params) use ($function) {
                unset($parameters);
                return $function(...$params);
            });
        }
    }

    private function registerPropertyFunctions()
    {
        $this->register('setProperty', function (string $property, string $value) {
            return sprintf('($entity->payload[%s] = %s)', $property, $value);
        }, function (array $parameters, string $property, string $value) {
            /** @var Entity $entity */
            $entity                     = $parameters['entity'];
            $entity->payload[$property] = $value;
        });

        $this->register('getProperty', function (string $property) {
            return sprintf('$entity->payload[%s]', $property);
        }, function (array $parameters, string $property) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];

            return $entity->payload[$property];
        });
    }

    private function registerCounter()
    {
        $this->register('increaseCounter', function () {
            throw new Exception('increaseCounter() not implemented');
        }, function (array $parameters) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];
            if (empty($entity->payload['counter'])) {
                $entity->payload['counter'] = 1;
            } else {
                $entity->payload['counter']++;
            }
        });
    }

    private function registerTiming()
    {
        $this->register('isTiming', function (string $timingId) {
            return sprintf('($eventName == \'%s\' && $event->getTimingId() === %s)', TimingEvent::TIMING_EVENT, $timingId);
        }, function (array $parameters, string $isTiming) {
            if ($parameters['eventName'] !== TimingEvent::TIMING_EVENT) {
                return false;
            }

            return $parameters['event']->getTimingId() === $isTiming;
        });
    }
}
