<?php

namespace Raspberry\Gpio;

use InvalidArgumentException;

class PinsCollection
{
    /**
     * @var Pin[]
     */
    private $pins = [];

    /**
     * @param Pin $pin
     */
    public function add(Pin $pin)
    {
        $pin_id = $pin->getID();
        $this->pins[$pin_id] = $pin;
    }

    /**
     * @param integer $id
     * @return Pin
     * @throws InvalidArgumentException
     */
    public function get($id)
    {
        if (empty($this->pins[$id])) {
            throw new InvalidArgumentException(sprintf('Pin #%s does not exist', $id));
        }

        return $this->pins[$id];
    }

    /**
     * @return Pin[]
     */
    public function getAll()
    {
        return $this->pins;
    }
}
