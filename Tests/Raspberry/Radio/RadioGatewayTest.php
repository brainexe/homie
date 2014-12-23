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
class RadioGatewayTest extends PHPUnit_Framework_TestCase
{

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

    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
        $this->mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new RadioGateway();
        $this->subject->setRedis($this->mockRedis);
        $this->subject->setIdGenerator($this->mockIdGenerator);
    }

    public function testGetRadios()
    {
        $radio_ids = [
        $radio_id = 1
        ];

        $result = ['result'];

        $this->mockRedis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(RadioGateway::REDIS_RADIO_IDS)
            ->willReturn($radio_ids);

        $this->mockRedis
            ->expects($this->once())
            ->method('multi')
            ->willReturn($this->mockRedis);

        $this->mockRedis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("radios:$radio_id");

        $this->mockRedis
            ->expects($this->once())
            ->method('exec')
            ->willReturn($result);

        $actualResult = $this->subject->getRadios();

        $this->assertEquals($result, $actualResult);
    }

    public function testGetRadio()
    {
        $radioId = 10;

        $radio = ['radio'];

        $this->mockRedis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("radios:$radioId")
            ->willReturn($radio);

        $actualResult = $this->subject->getRadio($radioId);

        $this->assertEquals($radio, $actualResult);
    }

    public function testGetRadioIds()
    {
        $radioIds = [
            1
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(RadioGateway::REDIS_RADIO_IDS)
            ->willReturn($radioIds);

        $actualResult = $this->subject->getRadioIds();

        $this->assertEquals($radioIds, $actualResult);
    }

    public function testAddRadio()
    {
        $radioId = "11880";

        $radioVo = new RadioVO();
        $radioVo->radioId     = $radioId;
        $radioVo->name        = $name = 'name';
        $radioVo->description = $description = 'description';
        $radioVo->pin         = $pin = 'pin';
        $radioVo->code        = $code = 'code';

        $this->mockIdGenerator
            ->expects($this->once())
            ->method('generateRandomId')
            ->willReturn($radioId);

        $this->mockRedis
            ->expects($this->once())
            ->method('multi')
            ->willReturn($this->mockRedis);

        $key = "radios:$radioId";

        $this->mockRedis
            ->expects($this->once())
            ->method('HMSET')
            ->with($key, [
                'radioId' => $radioId,
                'name' => $name,
                'description' => $description,
                'pin' => $pin,
                'code' => $code,
            ]);

        $this->mockRedis
            ->expects($this->once())
            ->method('SADD')
            ->with(RadioGateway::REDIS_RADIO_IDS, $radioId);

        $this->mockRedis
            ->expects($this->once())
            ->method('exec');

        $actualResult = $this->subject->addRadio($radioVo);

        $this->assertEquals($radioId, $actualResult);
    }

    public function testEditRadio()
    {
        $radioVo = new RadioVO();
        $radioVo->radioId = $radioId = 10;

        $this->mockRedis
            ->expects($this->once())
            ->method('hMset')
            ->with("radios:$radioId", [
                'radioId' => $radioId,
                'code' => null,
                'pin' => null,
                'name' => null,
                'description' => null,
            ]);

        $this->subject->editRadio($radioVo);
    }

    public function testDeleteRadio()
    {
        $radioId = 10;

        $this->mockRedis
            ->expects($this->once())
            ->method('SREM')
            ->with(RadioGateway::REDIS_RADIO_IDS, $radioId);

        $this->mockRedis
            ->expects($this->once())
            ->method('DEL')
            ->with("radios:$radioId");

        $this->subject->deleteRadio($radioId);
    }
}
