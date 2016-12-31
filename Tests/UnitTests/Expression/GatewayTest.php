<?php

namespace Tests\Homie\Expression;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Redis\Predis;
use BrainExe\Core\Util\IdGenerator;
use Homie\Expression\Entity;
use Homie\Expression\Gateway;
use Homie\Expression\Language;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

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

    /**
     * @var Language|MockObject
     */
    private $language;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setup()
    {
        $this->redis       = $this->getRedisMock();
        $this->idGenerator = $this->createMock(IdGenerator::class);
        $this->language    = $this->createMock(Language::class);
        $this->dispatcher  = $this->createMock(EventDispatcher::class);

        $this->subject = new Gateway($this->language);
        $this->subject->setRedis($this->redis);
        $this->subject->setIdGenerator($this->idGenerator);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testSave()
    {
        $id = 3333;

        $this->idGenerator
            ->expects($this->once())
            ->method('generateUniqueId')
            ->willReturn($id);

        $savedEntity = new Entity();
        $savedEntity->expressionId      = $id;
        $savedEntity->compiledCondition = null;

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(Gateway::REDIS_KEY, $id, serialize($savedEntity));

        $entity = new Entity();
        $this->subject->save($entity);
    }

    public function testDelete()
    {
        $id = 'id';

        $this->redis
            ->expects($this->once())
            ->method('hdel')
            ->willReturn(1);

        $this->subject->delete($id);
    }

    public function testGetAll()
    {

        $entity = new Entity();
        $entities = [
            $entityId = 'entityId' => serialize($entity)
        ];
        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with(Gateway::REDIS_KEY)
            ->willReturn($entities);

        $actual = $this->subject->getAll();

        $expected = [
            $entityId => $entity
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testGetEntities()
    {

        $entity = new Entity();
        $entities = [
            $entityId = 'entityId' => serialize($entity)
        ];
        $this->redis
            ->expects($this->once())
            ->method('hmget')
            ->with(Gateway::REDIS_KEY, [$entityId])
            ->willReturn($entities);

        $actual = $this->subject->getEntities([$entityId]);

        $expected = [
            $entityId => $entity
        ];

        $this->assertEquals($expected, $actual);
    }
}
