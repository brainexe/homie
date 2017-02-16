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
     * @var array
     */
    private $variables;

    /**
     * @param string $expression
     * @param array $variables
     */
    public function __construct(string $expression, array $variables = [])
    {
        parent::__construct(self::EVALUATE);

        $this->expression = $expression;
        $this->variables  = $variables;
    }

    /**
     * @return string
     */
    public function getExpression() : string
    {
        return $this->expression;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }
}
