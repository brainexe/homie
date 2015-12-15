<?php

namespace Tests\Homie\Radio;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Radio\Gateway;
use Homie\Radio\VO\RadioVO;
use BrainExe\Core\Util\IdGenerator;

/**
 * @covers Homie\Radio\Gateway
 */
class RadioGatewayTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Gateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    /**
     * @var IdGenerator|MockObject
     */
    private $idGenerator;

    public function setUp()
    {
        $this->redis       = $this->getRedisMock();
        $this->idGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

        $this->subject = new Gateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testGetRadios()
    {
        $radioIds = [
            $radioId = 1
        ];

        $result = ['result'];

        $this->redis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(Gateway::REDIS_SWITCH_IDS)
            ->willReturn($radioIds);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $this->redis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("switches:$radioId");

        $this->redis
            ->expects($this->once())
            ->method('execute')
            ->willReturn($result);

        $actualResult = $this->subject->getAll();

        $this->assertEquals($result, $actualResult);
    }

    public function testGetRadio()
    {
        $switchId = 10;

        $radio = ['radio'];

        $this->redis
            ->expects($this->once())
            ->method('HGETALL')
            ->with("switches:$switchId")
            ->willReturn($radio);

        $actualResult = $this->subject->get($switchId);

        $this->assertEquals($radio, $actualResult);
    }

    public function testGetRadioIds()
    {
        $radioIds = [
            1
        ];

        $this->redis
            ->expects($this->once())
            ->method('SMEMBERS')
            ->with(Gateway::REDIS_SWITCH_IDS)
            ->willReturn($radioIds);

        $actualResult = $this->subject->getIds();

        $this->assertEquals($radioIds, $actualResult);
    }

    public function testAddRadio()
    {
        $switchId = "11880";

        $radioVo = new RadioVO();
        $radioVo->switchId    = $switchId;
        $radioVo->name        = $name = 'name';
        $radioVo->description = $description = 'description';
        $radioVo->pin         = $pin = 'pin';
        $radioVo->code        = $code = 'code';

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($switchId);

        $this->redis
            ->expects($this->once())
            ->method('pipeline')
            ->willReturn($this->redis);

        $key = "switches:$switchId";

        $this->redis
            ->expects($this->once())
            ->method('HMSET')
            ->with($key, [
                'switchId' => $switchId,
                'name' => $name,
                'description' => $description,
                'pin' => $pin,
                'code' => $code,
                'status' => $radioVo->status,
                'type' => RadioVO::TYPE
            ]);

        $this->redis
            ->expects($this->once())
            ->method('SADD')
            ->with(Gateway::REDIS_SWITCH_IDS, [$switchId]);

        $this->redis
            ->expects($this->once())
            ->method('execute');

        $actualResult = $this->subject->add($radioVo);

        $this->assertEquals($switchId, $actualResult);
    }

    public function testEditRadio()
    {
        $radioVo = new RadioVO();
        $radioVo->switchId = $switchId = 10;
        $radioVo->status   = 1;

        $this->redis
            ->expects($this->once())
            ->method('hMset')
            ->with("switches:$switchId", [
                'switchId' => $switchId,
                'code' => null,
                'pin' => null,
                'name' => null,
                'description' => null,
                'status' => 1,
                'type' => RadioVO::TYPE
            ]);

        $this->subject->edit($radioVo);
    }

    public function testDeleteRadio()
    {
        $radioId = 10;

        $this->redis
            ->expects($this->once())
            ->method('SREM')
            ->with(Gateway::REDIS_SWITCH_IDS, $radioId);

        $this->redis
            ->expects($this->once())
            ->method('DEL')
            ->with("switches:$radioId");

        $this->subject->delete($radioId);
    }
}
