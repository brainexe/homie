<?php

namespace Homie\Expression\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class EvaluateEvent extends AbstractEvent
{
    const EVALUATE = 'expression.evaluate';

    /**
     * @var string
     */
    private $expression;

    /**
     * @param string $expression
     */
    public function __construct(string $expression)
    {
        parent::__construct(self::EVALUATE);

        $this->expression = $expression;
    }

    /**
     * @return string
     */
    public function getExpression() : string
    {
        return $this->expression;
    }
}
