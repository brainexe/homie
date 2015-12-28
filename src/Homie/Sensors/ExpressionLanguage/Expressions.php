<?php

namespace Homie\Sensors\ExpressionLanguage;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Expression\CompilerPass\DefaultExpression;
use BrainExe\Expression\Entity;

/**
 * @Service("Sensors.ExpressionLanguage", public=false, tags={{"name"="default_expressions"}})
 */
class Expressions implements DefaultExpression
{

    /**
     * @return Entity[]
     */
    public static function getDefaultExpressions()
    {
        $sensorCron = new Entity();
        $sensorCron->expressionId = 'sensorCron';
        $sensorCron->conditions = [
            'isTiming("minute")'
        ];
        $sensorCron->actions = [
            'event("console.run", "cron:sensor")'
        ];
        yield $sensorCron;

        $cleanCron = new Entity();
        $cleanCron->expressionId = 'cleanCron';
        $cleanCron->conditions = [
            'isTiming("daily")'
        ];
        $cleanCron->actions = [
            'event("console.run", "cron:clean")',
            'increaseCounter()'
        ];
        yield $cleanCron;
    }
}
