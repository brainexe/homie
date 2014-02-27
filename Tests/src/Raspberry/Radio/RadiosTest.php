<?php

namespace Raspberry\Tests\Radio;

use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\Radios;

class RadiosTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Radios
	 */
	private $_subject;

	/**
	 * @var RadioGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_radio_gateway;

	public function setUp() {
		$this->_mock_radio_gateway = $this->getMock('Raspberry\Radio\RadioGateway');

		$this->_subject = new Radios($this->_mock_radio_gateway);
	}

	/**
	 * @dataProvider providerPins
	 */
	public function testGetRadioPin($input_pin, $expected_pin) {
		if (false === $expected_pin) {
			$this->setExpectedException('\InvalidArgumentException');
		}
		$actual_pin = $this->_subject->getRadioPin($input_pin);

		$this->assertEquals($expected_pin, $actual_pin);
	}

	public function testAddRadio() {
		$name = 'foo';
		$description = 'foo extended';
		$code = '1101';
		$pin = 1;

		$radio_id = 12;

		$this->_mock_radio_gateway
			->expects($this->once())
			->method('addRadio')
			->with($name, $description, $code, $pin)
			->will($this->returnValue($radio_id));

		$actual_result = $this->_subject->addRadio($name, $description, $code, $pin);

		$this->assertEquals($radio_id, $actual_result);
	}

	public function testDeleteRadio() {
		$radio_id = 12;

		$this->_mock_radio_gateway
			->expects($this->once())
			->method('deleteRadio')
			->with($radio_id);

		$this->_subject->deleteRadio($radio_id);
	}

	public function testGetRadio() {
		$radio_id = 21;

		$radio = [
			'code' => '1011',
			'pin' => 1
		];
		$this->_mock_radio_gateway
			->expects($this->once())
			->method('getRadio')
			->with($radio_id)
			->will($this->returnValue($radio));

		$result = $this->_subject->getRadio($radio_id);

		$this->assertEquals($radio, $result);
	}

	/**
	 * @return array[]
	 */
	public static function providerPins() {
		return [
			[1, 1],
			[2, 2],
			[0, false],
			[0.5, false],
			['A', 1],
			['D', 4],
			['', false],
			['G', false],
		];
	}
} 
