<?php

namespace Homie\Expression\CompilerPass;

use Homie\Expression\Entity;

interface DefaultExpression
{

    /**
     * @return iterable|Entity[]
     */
    public static function getDefaultExpressions() : iterable;
}
