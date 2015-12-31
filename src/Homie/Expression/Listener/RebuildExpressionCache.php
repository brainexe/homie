<?php

namespace Homie\Expression\Listener;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class RebuildExpressionCache extends AbstractEvent
{
    const REBUILD = 'expression:cache:rebuild';

    public function __construct()
    {
        parent::__construct(self::REBUILD);
    }
}
