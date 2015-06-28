<?php

namespace Homie\Gpio;

use JsonSerializable;

class Pin implements JsonSerializable
{

    const DIRECTION_IN  = 'IN';
    const DIRECTION_OUT = 'OUT';

    /**
     * @var int
     */
    private $wiringId;

    /**
     * @var int
     */
    private $physicalId;

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
    private $mode;

    /**
     * @var boolean
     */
    protected $value;

    /**
     * Get ID value.
     * @return int
     */
    public function getWiringId()
    {
        return $this->wiringId;
    }

    /**
     * Set ID value.
     * @param int $pinId ID
     */
    public function setWiringId($pinId)
    {
        $this->wiringId = $pinId;
    }

    /**
     * Get Name value.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name value.
     * @param string $name Name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get Direction value.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set Direction value.
     *
     * @param string $mode Direction
     *
     * @return Pin
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param boolean $value Value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * @return int
     */
    public function getPhysicalId()
    {
        return $this->physicalId;
    }

    /**
     * @param int $physicalId
     */
    public function setPhysicalId($physicalId)
    {
        $this->physicalId = $physicalId;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'wiringId'    => $this->wiringId,
            'physicalId'  => $this->physicalId,
            'value'       => $this->value,
            'name'        => $this->name,
            'description' => $this->description,
            'mode'        => $this->mode == 'OUT' ? 1 : 0,
        ];
    }
}
