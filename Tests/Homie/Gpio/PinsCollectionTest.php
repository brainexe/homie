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
    public function testGetInvalidPin()
    {
        $pinId = 10;

        $pinCollection = new PinsCollection();
        $pinCollection->get($pinId);
    }

    public function testGetPin()
    {
        $pinId   = 10;
        $pinName = 'name';

        $pin = new Pin();
        $pin->setID($pinId);
        $pin->setName($pinName);

        $collection = new PinsCollection();
        $collection->add($pin);

        $actualResult = $collection->get($pinId);
        $jsonResult = $pin->jsonSerialize();

        $this->assertEquals($pin, $actualResult);
        $this->assertInternalType('array', $jsonResult);
        $this->assertEquals($pinId, $jsonResult['id']);
        $this->assertEquals($pinName, $actualResult->getName());
        $this->assertEquals($pinName, $jsonResult['name']);
    }
}
