<?php

namespace Tests\Homie\Expression;

use BrainExe\Core\EventDispatcher\AbstractEvent;
use Homie\Expression\Entity;
use Homie\Expression\Gateway;
use Homie\Expression\Listener;
use Homie\Expression\Language;
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
        $this->language  = $this->createMock(Language::class);
        $this->gateway   = $this->createMock(Gateway::class);
        $this->container = $this->createMock(Container::class);

        $this->subject = new Listener(
            $this->gateway,
            $this->language,
            $this->container
        );
    }

    public function testNoFunctions()
    {
        $this->subject->setCachedFunctions(null);

        $event = $this->createMock(AbstractEvent::class);

        $this->subject->dispatch('testinvalid', $event);
    }

    public function testInvalidEvent()
    {
        $this->subject->setCachedFunctions(null);

        $event = $this->createMock(AbstractEvent::class);

        $this->subject->dispatch('testinvalid', $event);
    }

    public function testEvent()
    {
        $event = $this->createMock(AbstractEvent::class);

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
