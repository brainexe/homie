<?php

namespace Raspberry\Dashboard;

use JsonSerializable;

abstract class AbstractWidget implements WidgetInterface, JsonSerializable
{

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
