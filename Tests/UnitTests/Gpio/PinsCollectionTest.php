<?php

namespace Tests\Homie\Gpio;

use PHPUnit_Framework_TestCase as TestCase;
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
        $pinCollection->getByWiringId($pinId);
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
        $pin->setPhysicalId(11880);

        $collection = new PinsCollection('type');
        $collection->add($pin);

        $actualResult = $collection->getByWiringId($pinId);
        $jsonResult = $pin->jsonSerialize();

        $this->assertEquals($pin, $actualResult);
        $this->assertInternalType('array', $jsonResult);
        $this->assertEquals($pinId, $jsonResult['softwareId']);
        $this->assertEquals($pinName, $actualResult->getName());
        $this->assertEquals($pinName, $jsonResult['name']);
        $this->assertEquals(11880, $pin->getPhysicalId());
        $this->assertEquals('type', $collection->getType());
    }
}
