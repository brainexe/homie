<?php

namespace Homie\Expression\Event;

use BrainExe\Core\EventDispatcher\AbstractEvent;

class VariableChangedEvent extends AbstractEvent
{
    const CHANGED = 'expressionVariable.changed';
    const DELETED = 'expressionVariable.deleted';

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $type
     * @param string $key
     * @param string $value
     */
    public function __construct(string $type, string  $key, string $value = null)
    {
        parent::__construct($type);

        $this->key   = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }
}
