<?php

namespace Tests\Homie\Display;

use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Tests\RedisMockTrait;
use Homie\Display\Gateway;
use Homie\Display\Settings;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Display\Gateway
 */
class GatewayTest extends TestCase
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
        $this->idGenerator = $this->createMock(IdGenerator::class);
        $this->subject = new Gateway();
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
    }

    public function testAddDisplay()
    {
        $setting = new Settings();

        $generatedId = 11222;

        $expected = new Settings();
        $expected->displayId = $generatedId;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($generatedId);

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(Gateway::KEY, $generatedId, serialize($expected));

        $this->subject->addDisplay($setting);
    }

    public function testGetAll()
    {
        $settings = new Settings();
        $settings->content = '1212';

        $list = [
            serialize($settings)
        ];
        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with(Gateway::KEY)
            ->willReturn($list);

        $actual = $this->subject->getAll();

        $this->assertEquals([$settings], iterator_to_array($actual));
    }
    public function testGet()
    {
        $displayId = 42;

        $settings = new Settings();
        $settings->content = '1212';

        $list = serialize($settings);
        $this->redis
            ->expects($this->once())
            ->method('hget')
            ->with(Gateway::KEY, $displayId)
            ->willReturn($list);

        $actual = $this->subject->get($displayId);

        $this->assertEquals($settings, $actual);
    }

    public function testDelete()
    {
        $displayId = 42;

        $this->redis
            ->expects($this->once())
            ->method('hdel')
            ->with(Gateway::KEY, [$displayId]);

        $this->subject->delete($displayId);
    }
}
