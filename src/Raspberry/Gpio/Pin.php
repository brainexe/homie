<?php

namespace Raspberry\Gpio;

use JsonSerializable;

class Pin implements JsonSerializable
{

    const DIRECTION_IN  = 'in';
    const DIRECTION_OUT = 'out';

    const VALUE_LOW  = 'LOW';
    const VALUE_HIGH = 'HIGH';

    /**
     * wiringPi ID.
     *
     * @var integer
     */
    private $sensorId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $direction;

    /**
     * @var boolean
     */
    protected $value;

    /**
     * Get ID value.
     *
     * @return integer
     */
    public function getID()
    {
        return $this->sensorId;
    }

    /**
     * Set ID value.
     *
     * @param integer $pinId ID
     *
     * @return Pin
     */
    public function setID($pinId)
    {
        $this->sensorId = $pinId;

        return $this;
    }

    /**
     * Get Name value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name value.
     *
     * @param string $name Name
     *
     * @return Pin
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Direction value.
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set Direction value.
     *
     * @param string $direction Direction
     *
     * @return Pin
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get Value value.
     *
     * @return boolean
     */
    public function isHighValue()
    {
        return $this->value;
    }

    /**
     * Set Value value.
     *
     * @param boolean $value Value
     *
     * @return Pin
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
        'id' => $this->sensorId,
        'value' => $this->value,
        'name' => $this->name,
        'description' => $this->description,
        'direction' => $this->direction == 'OUT' ? 1 : 0,
        ];
    }
}
