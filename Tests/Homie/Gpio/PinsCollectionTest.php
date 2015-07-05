<?php

namespace Tests\Homie\Gpio;

use PHPUnit_Framework_TestCase;
use Homie\Gpio\Pin;
use Homie\Gpio\PinsCollection;

class PinsCollectionTest extends PHPUnit_Framework_TestCase
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
    public function testGetInvalidPinPysical()
    {
        $pinId = 10;

        $pinCollection = new PinsCollection();
        $pinCollection->getByPhysicalId($pinId);
    }

    public function testGetPin()
    {
        $pinId   = 10;
        $pinName = 'name';

        $pin = new Pin();
        $pin->setWiringId($pinId);
        $pin->setName($pinName);
        $pin->setPhysicalId(11880);

        $collection = new PinsCollection();
        $collection->add($pin);

        $actualResult = $collection->getByWiringId($pinId);
        $jsonResult = $pin->jsonSerialize();

        $this->assertEquals($pin, $actualResult);
        $this->assertInternalType('array', $jsonResult);
        $this->assertEquals($pinId, $jsonResult['wiringId']);
        $this->assertEquals($pinName, $actualResult->getName());
        $this->assertEquals($pinName, $jsonResult['name']);
        $this->assertEquals(11880, $pin->getPhysicalId());
    }
}
