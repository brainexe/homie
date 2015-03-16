<?php

namespace Raspberry\Sensors\Interfaces;

interface Searchable extends Parameterized
{

    /**
     * @return string[]
     */
    public function search();
}
