<?php

namespace Tests\Raspberry\Radio\RadioGateway;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\VO\RadioVO;
use Redis;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers Raspberry\Radio\RadioGateway
 */
class RadioGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RadioGateway
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	/**
	 * @var IdGenerator|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockIdGenerator;

	public function setUp() {

		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);
		$this->_subject = new RadioGateway();
		$this->_subject->setRedis($this->_mockRedis);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
	}

	public function testGetRadios() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getRadios();
	}

	public function testGetRadio() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getRadio($radio_id);
	}

	public function testGetRadioIds() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getRadioIds();
	}

	public function testAddRadio() {
		$id = "11880";

		$radio_vo = new RadioVO();
		$radio_vo->id = $id;
		$radio_vo->name = $name = 'name';
		$radio_vo->description = $description = 'description';
		$radio_vo->pin = $pin = 'pin';
		$radio_vo->code = $code = 'code';

		$this->_mockIdGenerator
			->expects($this->once())
			->method('generateRandomId')
			->will($this->returnValue($id));

		$this->_mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->_mockRedis));

		$key = "radios:$id";

		$this->_mockRedis
			->expects($this->once())
			->method('HMSET')
			->with($key, [
				'id' => $id,
				'name' => $name,
				'description' => $description,
				'pin' => $pin,
				'code' => $code,
			]);

		$this->_mockRedis
			->expects($this->once())
			->method('SADD')
			->with(RadioGateway::REDIS_RADIO_IDS, $id);

		$this->_mockRedis
			->expects($this->once())
			->method('exec');

		$actual_result = $this->_subject->addRadio($radio_vo);

		$this->assertEquals($id, $actual_result);
	}

	public function testEditRadio() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->editRadio($radio_vo);
	}

	public function testDeleteRadio() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->deleteRadio($radio_id);
	}

}
