<?php

namespace Tests\Raspberry\Gpio\PinGateway;

use PHPUnit_Framework_TestCase;
use Raspberry\Gpio\Pin;
use Raspberry\Gpio\PinsCollection;

class PinsCollectionTest extends PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Pin #10 does not exist
	 */
	public function testGetInvalidPin() {
		$pinId = 10;

		$pinCollection = new PinsCollection();
		$pinCollection->get($pinId);
	}

	public function testGetPin() {
		$pin_id = 10;

		$pin = new Pin();
		$pin->setID($pin_id);

		$pin_collection = new PinsCollection();
		$pin_collection->add($pin);

		$actual_result = $pin_collection->get($pin_id);
		$json_result = $pin->jsonSerialize();

		$this->assertEquals($pin, $actual_result);
		$this->assertInternalType('array', $json_result);
		$this->assertEquals($pin_id, $json_result['id']);
	}
}