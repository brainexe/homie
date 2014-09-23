<?php

namespace Raspberry\Tests\Radio;

use BrainExe\Core\Application\UserException;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\Radios;
use Raspberry\Radio\VO\RadioVO;

class RadiosTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Radios
	 */
	private $_subject;

	/**
	 * @var RadioGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mock_radio_gateway;

	public function setUp() {
		$this->_mock_radio_gateway = $this->getMock(RadioGateway::class);

		$this->_subject = new Radios($this->_mock_radio_gateway);
	}

	/**
	 * @dataProvider providerPins
	 * @param string $input_pin
	 * @param strig $expected_pin
	 * @throws UserException
	 */
	public function testGetRadioPin($input_pin, $expected_pin) {
		if (false === $expected_pin) {
			$this->setExpectedException(UserException::class);
		}
		$actual_pin = $this->_subject->getRadioPin($input_pin);

		$this->assertEquals($expected_pin, $actual_pin);
	}

	public function testGetRadios() {
		$radio = [
			'id' => 1,
			'name' => 'test',
			'description' => 'description',
			'pin' => 100,
			'code' => 1
		];

		$this->_mock_radio_gateway
			->expects($this->once())
			->method('getRadios')
			->will($this->returnValue([$radio]));

		$actual_result = $this->_subject->getRadios();

		$expected = new RadioVO();
		$expected->id = $radio['id'];
		$expected->name = $radio['name'];
		$expected->description = $radio['description'];
		$expected->pin = $radio['pin'];
		$expected->code = $radio['code'];

		$this->assertEquals([$radio['id'] => $expected], $actual_result);
	}

	public function testAddRadio() {
		$radio_vo = new RadioVO();
		$radio_vo->name = 'foo';
		$radio_vo->description = 'foo extended';
		$radio_vo->code = '1101';
		$radio_vo->pin = 1;

		$radio_id = 12;

		$this->_mock_radio_gateway
			->expects($this->once())
			->method('addRadio')
			->with($radio_vo)
			->will($this->returnValue($radio_id));

		$actual_result = $this->_subject->addRadio($radio_vo);

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
			'id' => $radio_id,
			'name' => 'test',
			'description' => 'description',
			'pin' => 100,
			'code' => 1
		];

		$this->_mock_radio_gateway
			->expects($this->once())
			->method('getRadio')
			->with($radio_id)
			->will($this->returnValue($radio));

		$result = $this->_subject->getRadio($radio_id);

		$radio_vo = new RadioVO();
		$radio_vo->id = $radio_id;
		$radio_vo->name = $radio['name'];
		$radio_vo->description = $radio['description'];
		$radio_vo->code = $radio['code'];
		$radio_vo->pin = $radio['pin'];

		$this->assertEquals($radio_vo, $result);
	}

	/**
	 * @return array[]
	 */
	public static function providerPins() {
		return [
			[1, 1],
			[2, 2],
			["2", 2],
			[0, false],
			[0.5, false],
			['A', 1],
			['D', 4],
			['', false],
			['G', false],
		];
	}
} 
