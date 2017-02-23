<?php

namespace Homie\Sensors\ExpressionLanguage;

use BrainExe\Core\Annotations\Service;
use Generator;
use Homie\Expression\CompilerPass\DefaultExpression;
use Homie\Expression\Entity;

/**
 * @Service("Sensors.ExpressionLanguage", tags={{"name"="default_expressions"}})
 */
class Expressions implements DefaultExpression
{

    /**
     * @return Generator|Entity[]
     */
    public static function getDefaultExpressions() : iterable
    {
        $sensorCron = new Entity();
        $sensorCron->expressionId = 'sensorCron';
        $sensorCron->conditions = [
            'isTiming("minute")'
        ];
        $sensorCron->actions = [
            'console("cron:sensor")',
        ];
        yield $sensorCron;

        $cleanCron = new Entity();
        $cleanCron->expressionId = 'cleanCron';
        $cleanCron->conditions = [
            'isTiming("daily")'
        ];
        $cleanCron->actions = [
            'console("cron:clean")',
            'console("messagequeue:clean")',
        ];

        yield $cleanCron;
    }
}
