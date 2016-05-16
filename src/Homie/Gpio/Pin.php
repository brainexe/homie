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
    private $softwareId;

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
    public function getSoftwareId() : int
    {
        return $this->softwareId;
    }

    /**
     * Set ID value.
     * @param int $pinId ID
     */
    public function setSoftwareId(int $pinId)
    {
        $this->softwareId = $pinId;
    }

    /**
     * Get Name value.
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set Name value.
     * @param string $name Name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get Direction value.
     *
     * @return string
     */
    public function getMode() : string
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
    public function setMode(string $mode) : Pin
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
    public function setDescription(string $description)
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
    public function setPhysicalId(int $physicalId)
    {
        $this->physicalId = $physicalId;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'softwareId'  => $this->softwareId,
            'physicalId'  => $this->physicalId,
            'value'       => $this->value,
            'name'        => $this->name,
            'description' => $this->description,
            'mode'        => $this->mode == 'OUT' ? 1 : 0,
        ];
    }
}
