<?php

namespace Homie\Dashboard;

use JsonSerializable;

abstract class AbstractWidget implements WidgetInterface, JsonSerializable
{

    const TYPE = 'none';

    /**
     * @return string
     */
    public function getId()
    {
        return static::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $payload)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $payload)
    {
        return true;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getMetadata();
    }
}
