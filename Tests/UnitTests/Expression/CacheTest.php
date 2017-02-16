<?php

namespace Tests\Homie\Expression;

use Homie\Expression\Cache;
use Homie\Expression\Entity;
use Homie\Expression\Gateway;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{

    /**
     * @var Cache|MockObject
     */
    private $subject;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    public function setup()
    {
        $this->gateway = $this->createMock(Gateway::class);

        $this->subject = $this->getMockBuilder(Cache::class)
            ->setMethods(['dumpCacheFile'])
            ->setConstructorArgs([$this->gateway])
            ->getMock();
    }

    public function testSave()
    {
        $expression1 = new Entity();
        $expression1->compiledCondition = 'compiled';
        $expression1->enabled = true;

        $expression2 = new Entity();
        $expression2->enabled = false;

        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$expression1, $expression2]);

        $this->subject
            ->expects($this->once())
            ->method('dumpCacheFile')
            ->with(Cache::CACHE_FILE, $this->isType('string'));

        $this->subject->writeCache();
    }
}
