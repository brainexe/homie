<?php

namespace Homie\Gpio;

use InvalidArgumentException;

class PinsCollection
{
    /**
     * @var Pin[]
     */
    private $pins = [];

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     */
    public function __construct(string $type = '')
    {
        $this->type = $type;
    }

    /**
     * @param Pin $pin
     */
    public function add(Pin $pin)
    {
        $pinId = $pin->getPhysicalId();
        $this->pins[$pinId] = $pin;
    }

    /**
     * @param int $pinId
     * @return Pin
     * @throws InvalidArgumentException
     */
    public function getByPhysicalId(int $pinId) : Pin
    {
        if (empty($this->pins[$pinId])) {
            throw new InvalidArgumentException(sprintf('Pin #%s does not exist', $pinId));
        }

        return $this->pins[$pinId];
    }

    /**
     * @param int $pinId
     * @return Pin
     * @throws InvalidArgumentException
     */
    public function getByWiringId(int $pinId) : Pin
    {
        foreach ($this->pins as $pin) {
            if ($pin->getWiringId() === $pinId) {
                return $pin;
            }
        }

        throw new InvalidArgumentException(sprintf('Pin #%s does not exist', $pinId));
    }

    /**
     * @return Pin[]
     */
    public function getAll()
    {
        return $this->pins;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
