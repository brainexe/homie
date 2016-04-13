<?php

namespace Tests\Homie\Expression;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use BrainExe\Core\EventDispatcher\Events\ClearCacheEvent;
use Homie\Expression\Entity;
use Homie\Expression\Gateway;
use Homie\Expression\Listener;
use Homie\Expression\Language;
use Homie\Sensors\GetValue\Event;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;

class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var Container|MockObject
     */
    private $container;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    /**
     * @var Language|MockObject
     */
    private $language;

    public function setup()
    {
        $this->language  = $this->getMock(Language::class, [], [], '', false);
        $this->gateway   = $this->getMock(Gateway::class, [], [], '', false);
        $this->container = $this->getMock(Container::class, [], [], '', false);

        $this->subject = new Listener(
            $this->gateway,
            $this->language,
            $this->container
        );
    }

    public function testNoFunctions()
    {
        $this->subject->setCachedFunctions(null);

        $event = $this->getMock(AbstractEvent::class, [], ['testinvalid']);

        $this->subject->dispatch('testinvalid', $event);
    }

    public function testInvalidEvent()
    {
        $this->subject->setCachedFunctions(null);

        $event = $this->getMock(AbstractEvent::class, [], ['testinvalid']);

        $this->subject->dispatch('testinvalid', $event);
    }

    public function testEvent()
    {
        $event = $this->getMock(AbstractEvent::class, [], ['event']);

        $entity = new Entity();
        $entity->actions = ['action'];

        $this->language
            ->expects($this->once())
            ->method('evaluate')
            ->with('action', [
                'event' => $event,
                'eventName' => 'event',
                'entity' => $entity
            ]);

        $this->subject->setCachedFunctions(function () use ($entity) {
            yield $entity;
        });

        $this->subject->dispatch('event', $event);
    }
}
