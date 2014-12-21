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
        $pinId = $pin->getID();
        $this->pins[$pinId] = $pin;
    }

    /**
     * @param integer $pinId
     * @return Pin
     * @throws InvalidArgumentException
     */
    public function get($pinId)
    {
        if (empty($this->pins[$pinId])) {
            throw new InvalidArgumentException(sprintf('Pin #%s does not exist', $pinId));
        }

        return $this->pins[$pinId];
    }

    /**
     * @return Pin[]
     */
    public function getAll()
    {
        return $this->pins;
    }
}
