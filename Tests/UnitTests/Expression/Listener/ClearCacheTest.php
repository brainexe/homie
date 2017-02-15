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
        $this->cache   = $this->createMock(Cache::class);
        $this->gateway = $this->createMock(Gateway::class);
        $this->logger  = $this->createMock(Logger::class);

        $this->subject = new ClearCache(
            $this->cache,
            $this->gateway,
            $this->logger
        );
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
