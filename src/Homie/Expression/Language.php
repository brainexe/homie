<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\Events\TimingEvent;
use BrainExe\Core\Traits\EventDispatcherTrait;
use BrainExe\Core\Traits\LoggerTrait;
use BrainExe\InputControl\InputControlEvent;
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
        ];

        foreach ($functions as $function) {
            $this->register($function, function () use ($function) {
                $parameters = func_get_args();

                return sprintf('%s(%s)', $function, implode(', ', $parameters));
            }, function () use ($function) {
                $parameters = array_slice(func_get_args(), 1);
                return call_user_func_array($function, $parameters);
            });
        }

        $this->register('setProperty', function ($property, $value) {
            return sprintf('($entity->payload[%s] = %s)', $property, $value);
        }, function ($parameters, $property, $value) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];
            $entity->payload[$property] = $value;
        });

        $this->register('getProperty', function ($property) {
            return sprintf('$entity->payload[%s]', $property);
        }, function ($parameters, $property) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];

            return $entity->payload[$property];
        });

        $this->register('isTiming', function ($timingId) {
            return sprintf('($eventName == \'%s\' && $event->timingId === %s)', TimingEvent::TIMING_EVENT, $timingId);
        }, function ($parameters, $isTiming) {
            if ($parameters['eventName'] !== TimingEvent::TIMING_EVENT) {
                return false;
            }

            return $parameters['event']->timingId === $isTiming;
        });

        $this->register('isEvent', function ($eventId) {
            return sprintf('($eventName == %s)', $eventId);
        }, function ($parameters, $eventId) {
            return $parameters['eventName'] === $eventId;
        });

        $this->register('exec', function () {
            throw new Exception('exec() not implemented');
        }, function ($parameters, $string) {
            unset($parameters);
            $inputEvent = new InputControlEvent($string);

            $this->getDispatcher()->dispatchInBackground($inputEvent);
        });

        $this->register('event', function () {
            throw new Exception('event() not implemented');
        }, function (array $parameters, $type, ...$eventArguments) {
            unset($parameters);
            $events = (include ROOT.'/cache/events.php'); // TODO extract

            $reflection = new ReflectionClass($events[$type]['class']);
            /** @var AbstractEvent $event */
            $event = $reflection->newInstanceArgs($eventArguments);

            $this->dispatchEvent($event);
        });

        $this->register('log', function () {
            throw new Exception('log() not implemented');
        }, function (array $parameters, $level, $message, $context = null) {
            unset($parameters);
            $this->log($level, $message, ['channel' => $context]);
        });

        $this->register('increaseCounter', function () {
            throw new Exception('log() not implemented');
        }, function (array $parameters) {
            /** @var Entity $entity */
            $entity = $parameters['entity'];
            if (empty($entity->payload['counter'])) {
                $entity->payload['counter'] = 1;
            } else {
                $entity->payload['counter']++;
            }
        });

        $this->register('executeExpression', function () {
            throw new Exception('executeExpression() not implemented');
        }, function (array $parameters, $expressionId) {
            // TODO throw some event
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
     * @deprecated
     * @return string[]
     */
    public function getFunctionNames()
    {
        return array_keys($this->functions);
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
