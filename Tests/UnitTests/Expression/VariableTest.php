<?php

namespace Tests\Homie\Expression;

use BrainExe\Tests\RedisMockTrait;
use Homie\Expression\Variable;
use PHPUnit_Framework_TestCase as TestCase;
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

    public function setup()
    {
        $this->predis = $this->getRedisMock();

        $this->subject = new Variable();
        $this->subject->setRedis($this->predis);
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

        $actual = $this->subject->setVariable('key', 'value');

        $this->assertNull($actual);
    }

    public function testDelete()
    {
        $this->predis
            ->expects($this->once())
            ->method('hdel')
            ->with(Variable::REDIS_KEY, 'key');

        $actual = $this->subject->deleteVariable('key');

        $this->assertNull($actual);
    }
}
