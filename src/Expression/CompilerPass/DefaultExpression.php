<?php

namespace Homie\Expression\CompilerPass;

use Homie\Expression\Entity;

interface DefaultExpression
{

    /**
     * @return Entity[]
     */
    public static function getDefaultExpressions() : iterable;
}
