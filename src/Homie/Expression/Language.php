<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\LoggerTrait;
use Exception;
use ReflectionClass;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ParserCache\ParserCacheInterface;

/**
 * @Service("Expression.Language", public=false, lazy=true)
 */
class Language extends ExpressionLanguage
{

    use EventDispatcherTrait;
    use LoggerTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(ParserCacheInterface $cache = null, array $providers = [])
    {
        parent::__construct($cache, $providers);

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

        $this->register('setProperty', function (string $property, string $value) {
            return sprintf('($entity->payload[%s] = %s)', $property, $value);
        }, function (array $parameters, string $property, string $value) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];
            $entity->payload[$property] = $value;
        });

        $this->register('getProperty', function (string $property) {
            return sprintf('$entity->payload[%s]', $property);
        }, function (array $parameters, string $property) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];

            return $entity->payload[$property];
        });

        $this->register('isTiming', function (string $timingId) {
            return sprintf('($eventName == \'%s\' && $event->timingId === %s)', TimingEvent::TIMING_EVENT, $timingId);
        }, function (array $parameters, string $isTiming) {
            if ($parameters['eventName'] !== TimingEvent::TIMING_EVENT) {
                return false;
            }

            return $parameters['event']->timingId === $isTiming;
        });

        $this->register('isEvent', function (string $eventId) {
            return sprintf('($eventName == %s)', $eventId);
        }, function (array $parameters, string $eventId) {
            return $parameters['eventName'] === $eventId;
        });

        $this->register('event', function () {
            throw new Exception('event() not implemented');
        }, function (array $parameters, string $type, ...$eventArguments) {
            unset($parameters);
            $events = (include ROOT.'/cache/events.php'); // TODO extract

            $reflection = new ReflectionClass($events[$type]['class']);
            /** @var AbstractEvent $event */
            $event = $reflection->newInstanceArgs($eventArguments);

            $this->dispatchEvent($event);
        });

        $this->register('log', function () {
            throw new Exception('log() not implemented');
        }, function (array $parameters, $level, string $message, $context = null) {
            unset($parameters);
            $this->log($level, $message, ['channel' => $context]);
        });

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

    /**
     * @param string|Expression $expression
     * @param array $values
     * @return string
     */
    public function evaluate($expression, $values = array())
    {
        if (!$expression) {
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
}
