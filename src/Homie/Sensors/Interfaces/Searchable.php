<?php

namespace Homie\Sensors\Interfaces;

interface Searchable extends Parameterized
{

    /**
     * @return string[]
     */
    public function search() : array;
}
