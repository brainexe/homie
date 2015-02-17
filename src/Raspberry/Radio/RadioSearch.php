<?php

namespace Raspberry\Radio;

use BrainExe\Annotations\Annotations\Inject;
use BrainExe\Search\Annotations\Search;
use BrainExe\Search\Input;

/**
 * @Search("Radio.Search")
 */
class RadioSearch implements Input
{

    /**
     * @var Radios
     */
    private $radios;

    /**
     * @Inject("@Radios")
     * @param Radios $radios
     */
    public function __construct(Radios $radios)
    {
        $this->radios = $radios;
    }

    /**
     * @return array[]
     */
    public function getData()
    {
        $result = [];
        foreach ($this->radios->getRadios() as $radio) {
            $result[] = [
                'body'  => ['name' => $radio->name],
                'index' => 'radio',
                'type'  => 'radio',
                'id'    => $radio->radioId
            ];
        }

        return $result;
    }
}
