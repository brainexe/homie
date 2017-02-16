<?php

namespace Tests\Homie\Gpio;

use PHPUnit\Framework\TestCase;
use Homie\Gpio\Pin;
use Homie\Gpio\PinsCollection;

class PinsCollectionTest extends TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Pin #10 does not exist
     */
    public function testGetInvalidPinWiring()
    {
        $pinId = 10;

        $pinCollection = new PinsCollection();
        $pinCollection->getBySoftwareId($pinId);
    }
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Pin #10 does not exist
     */
    public function testGetInvalidPinPhysical()
    {
        $pinId = 10;

        $pinCollection = new PinsCollection();
        $pinCollection->getByPhysicalId($pinId);
    }

    public function testGetPinPhysical()
    {
        $pinId = 10;
        $pin = new Pin();
        $pin->setPhysicalId($pinId);

        $pinCollection = new PinsCollection();
        $pinCollection->add($pin);
        $actual = $pinCollection->getByPhysicalId($pinId);

        $this->assertEquals($pin, $actual);
    }

    public function testGetPin()
    {
        $pinId   = 10;
        $pinName = 'name';

        $pin = new Pin();
        $pin->setSoftwareId($pinId);
        $pin->setName($pinName);
        $pin->setValue(2);
        $pin->setMode(Pin::DIRECTION_OUT);
        $pin->setPhysicalId(11880);

        $collection = new PinsCollection('type');
        $collection->add($pin);

        $actual = $collection->getBySoftwareId($pinId);
        $jsonResult = $pin->jsonSerialize();

        $this->assertEquals($pin, $actual);
        $this->assertInternalType('array', $jsonResult);
        $this->assertEquals($pinId, $jsonResult['softwareId']);
        $this->assertEquals($pinName, $actual->getName());
        $this->assertEquals(2, $actual->getValue());
        $this->assertEquals(Pin::DIRECTION_OUT, $actual->getMode());
        $this->assertEquals($pinName, $jsonResult['name']);
        $this->assertEquals(11880, $pin->getPhysicalId());
        $this->assertEquals('type', $collection->getType());
    }
}
