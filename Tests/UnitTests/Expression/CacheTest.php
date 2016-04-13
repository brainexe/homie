<?php

namespace Tests\Homie\Expression;

use Homie\Expression\Cache;
use Homie\Expression\Entity;
use Homie\Expression\Gateway;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

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
        $this->gateway = $this->getMock(Gateway::class, [], [], '', false);

        $this->subject = $this->getMock(Cache::class, ['dumpCacheFile'], [$this->gateway]);
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
