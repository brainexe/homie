<?php

namespace Homie\Expression;

use BrainExe\Annotations\Annotations\Service;
use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service("Expression.Variable", public=true)
 */
class Variable
{

    const REDIS_KEY = 'variable';

    use RedisTrait;

    public function setVariable(string $key, string $value)
    {
    }
    public function getVariable(string $key)
    {
    }
}
