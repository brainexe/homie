<?php

namespace Tests\Homie\Expression\Listener;

use Homie\Expression\Cache;
use Homie\Expression\Entity;
use Homie\Expression\Gateway;
use Homie\Expression\Listener\ClearCache;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class ClearCacheTest extends TestCase
{

    /**
     * @var ClearCache
     */
    private $subject;

    /**
     * @var Cache|MockObject
     */
    private $cache;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    /**
     * @var Logger|MockObject
     */
    private $logger;

    public function setup()
    {
        $this->cache   = $this->getMock(Cache::class, [], [], '', false);
        $this->gateway = $this->getMock(Gateway::class, [], [], '', false);
        $this->logger  = $this->getMock(Logger::class, [], [], '', false);

        $this->subject = new ClearCache(
            $this->cache,
            $this->gateway
        );
        $this->subject->setLogger($this->logger);
    }

    public function testRebuild()
    {
        $entities = [];
        $entity1 = $entities[] = new Entity();
        $entity1->expressionId = 'sensorCron';

        $this->cache
            ->expects($this->once())
            ->method('writeCache');
        $this->gateway
            ->expects($this->once())
            ->method('getAll')
            ->willReturn($entities);

        $this->subject->rebuildCache();

    }
}
