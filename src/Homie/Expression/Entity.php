<?php

namespace Homie\Expression;

class Entity
{

    /**
     * @var int|string
     */
    public $expressionId;

    /**
     * @var string[]
     */
    public $conditions = [];

    /**
     * @var string[]
     */
    public $actions = [];

    /**
     * @var mixed[]
     */
    public $payload = [];

    /**
     * @var string
     */
    public $compiledCondition;

    /**
     * @var bool
     */
    public $enabled = true;

    /**
     * @param array $array
     * @return static
     */
    public static function __set_state(array $array)
    {
        $entity = new static();
        foreach ($array as $key => $value) {
            $entity->$key = $value;
        }

        return $entity;
    }
}
