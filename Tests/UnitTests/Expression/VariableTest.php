<?php

namespace Tests\Homie\Expression;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Tests\RedisMockTrait;
use Homie\Expression\Event\VariableChangedEvent;
use Homie\Expression\Variable;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Predis\Client;

class VariableTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Variable
     */
    private $subject;

    /**
     * @var MockObject|Client
     */
    private $predis;

    /**
     * @var MockObject|EventDispatcher
     */
    private $dispatcher;

    public function setup()
    {
        $this->predis = $this->getRedisMock();
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Variable();
        $this->subject->setRedis($this->predis);
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetAll()
    {
        $array = ['key' => 'value'];

        $this->predis
            ->expects($this->once())
            ->method('hgetall')
            ->with(Variable::REDIS_KEY)
            ->willReturn($array);

        $actual = $this->subject->getAll();

        $this->assertEquals($array, $actual);
    }

    public function testSet()
    {
        $this->predis
            ->expects($this->once())
            ->method('hset')
            ->with(Variable::REDIS_KEY, 'key', 'value');

        $event = new VariableChangedEvent(VariableChangedEvent::CHANGED, 'key', 'value');
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->setVariable('key', 'value');
    }

    public function testDelete()
    {
        $this->predis
            ->expects($this->once())
            ->method('hdel')
            ->with(Variable::REDIS_KEY, ['key']);

        $event = new VariableChangedEvent(VariableChangedEvent::DELETED, 'key');
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->deleteVariable('key');
    }
}
