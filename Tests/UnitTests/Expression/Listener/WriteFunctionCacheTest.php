<?php

namespace Tests\Homie\Expression\Listener;

use Homie\Expression\Language;
use Homie\Expression\Listener\WriteFunctionCache;
use Monolog\Logger;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

class WriteFunctionCacheTest extends TestCase
{

    /**
     * @var WriteFunctionCache|MockObject
     */
    private $subject;

    /**
     * @var Logger|MockObject
     */
    private $language;

    public function setup()
    {
        $this->language = $this->createMock(Language::class);

        $this->subject = $this->getMockBuilder(WriteFunctionCache::class)
            ->setMethods(['dumpVariableToCache'])
            ->setConstructorArgs([$this->language])
            ->getMock();
    }

    public function testRebuildCacheEmpty()
    {
        $this->language
            ->expects($this->once())
            ->method('getFunctions')
            ->willReturn([]);

        $this->subject
            ->expects($this->once())
            ->method('dumpVariableToCache')
            ->with(WriteFunctionCache::CACHE, []);

        $this->subject->rebuildCache();
    }

    public function testRebuildCache()
    {
        $function = [];
        $function['evaluator'] = function (string $data, int $test1, array $test2) {
        };
        $function['compiler'] = function (string $data, int $test1, array $test2) {
        };

        $this->language
            ->expects($this->once())
            ->method('getFunctions')
            ->willReturn(['function' => $function]);

        $this->subject
            ->expects($this->once())
            ->method('dumpVariableToCache')
            ->with(WriteFunctionCache::CACHE, [
                'function' => [
                    'parameters' => [
                        ['name' => 'test1', 'type' => 'int'],
                        ['name' => 'test2', 'type' => 'array']
                    ],
                    'isAction'  => true,
                    'isTrigger' => true,
                ]
            ]);

        $this->subject->rebuildCache();
    }
}
