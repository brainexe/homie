<?php

namespace Homie\Expression\Listener;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class RebuildExpressionCache extends AbstractEvent
{
    const REBUILD = 'expressionCache.rebuild';

    public function __construct()
    {
        parent::__construct(self::REBUILD);
    }
}
