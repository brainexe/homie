<?php

namespace Tests\Raspberry\Radio\RadioGateway;

use BrainExe\Core\Redis\Redis;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Radio\RadioGateway;
use Raspberry\Radio\VO\RadioVO;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers Raspberry\Radio\RadioGateway
 */
class RadioGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var RadioGateway
	 */
	private $subject;

	/**
	 * @var Redis|MockObject
	 */
	private $mockRedis;

	/**
	 * @var IdGenerator|MockObject
	 */
	private $mockIdGenerator;

	public function setUp() {
		$this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

		$this->subject = new RadioGateway();
		$this->subject->setRedis($this->mockRedis);
		$this->subject->setIdGenerator($this->mockIdGenerator);
	}

	public function testGetRadios() {
		$radio_ids = [
			$radio_id = 1
		];

		$result = ['result'];

		$this->mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(RadioGateway::REDIS_RADIO_IDS)
			->will($this->returnValue($radio_ids));

		$this->mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->mockRedis));

		$this->mockRedis
			->expects($this->once())
			->method('HGETALL')
			->with("radios:$radio_id");

		$this->mockRedis
			->expects($this->once())
			->method('exec')
			->will($this->returnValue($result));

		$actual_result = $this->subject->getRadios();

		$this->assertEquals($result, $actual_result);
	}

	public function testGetRadio() {
		$radio_id = 10;

		$radio = ['radio'];

		$this->mockRedis
			->expects($this->once())
			->method('HGETALL')
			->with("radios:$radio_id")
			->will($this->returnValue($radio));

		$actual_result = $this->subject->getRadio($radio_id);

		$this->assertEquals($radio, $actual_result);
	}

	public function testGetRadioIds() {
		$radio_ids = [
			$radio_id = 1
		];

		$this->mockRedis
			->expects($this->once())
			->method('SMEMBERS')
			->with(RadioGateway::REDIS_RADIO_IDS)
			->will($this->returnValue($radio_ids));

		$actual_result = $this->subject->getRadioIds();

		$this->assertEquals($radio_ids, $actual_result);
	}

	public function testAddRadio() {
		$id = "11880";

		$radio_vo = new RadioVO();
		$radio_vo->id = $id;
		$radio_vo->name = $name = 'name';
		$radio_vo->description = $description = 'description';
		$radio_vo->pin = $pin = 'pin';
		$radio_vo->code = $code = 'code';

		$this->mockIdGenerator
			->expects($this->once())
			->method('generateRandomId')
			->will($this->returnValue($id));

		$this->mockRedis
			->expects($this->once())
			->method('multi')
			->will($this->returnValue($this->mockRedis));

		$key = "radios:$id";

		$this->mockRedis
			->expects($this->once())
			->method('HMSET')
			->with($key, [
				'id' => $id,
				'name' => $name,
				'description' => $description,
				'pin' => $pin,
				'code' => $code,
			]);

		$this->mockRedis
			->expects($this->once())
			->method('SADD')
			->with(RadioGateway::REDIS_RADIO_IDS, $id);

		$this->mockRedis
			->expects($this->once())
			->method('exec');

		$actual_result = $this->subject->addRadio($radio_vo);

		$this->assertEquals($id, $actual_result);
	}

	public function testEditRadio() {
		$radio_vo = new RadioVO();
		$radio_vo->id = $radio_id = 10;

		$this->mockRedis
			->expects($this->once())
			->method('hMset')
			->with("radios:$radio_id", [
				'id' => $radio_id,
				'code' => null,
				'pin' => null,
				'name' => null,
				'description' => null,
			]);

		$this->subject->editRadio($radio_vo);
	}

	public function testDeleteRadio() {
		$radio_id = 10;

		$this->mockRedis
			->expects($this->once())
			->method('SREM')
			->with(RadioGateway::REDIS_RADIO_IDS, $radio_id);

		$this->mockRedis
			->expects($this->once())
			->method('DEL')
			->with("radios:$radio_id");

		$this->subject->deleteRadio($radio_id);
	}

}
