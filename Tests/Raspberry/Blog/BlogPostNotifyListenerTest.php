<?php

namespace Tests\Raspberry\Blog\BlogPostNotifyListener;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Blog\BlogPostNotifyListener;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\Blog\BlogPostVO;
use Raspberry\Blog\Events\BlogEvent;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;

class BlogPostNotifyListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var BlogPostNotifyListener
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    public function setUp()
    {
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
        $this->mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new BlogPostNotifyListener();
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
        $this->subject->setTime($this->mockTime);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandlePostEvent()
    {
        $userVo = new UserVO();
        $postVo = new BlogPostVO();
        $postVo->mood = $mood = 10;

        $event = new BlogEvent($userVo, $postVo);

        $hour   = 12;
        $minute = 50;

        $this->mockTime
            ->expects($this->at(0))
            ->method('date')
            ->with('G')
            ->willReturn($hour);

        $this->mockTime
        ->expects($this->at(1))
        ->method('date')
        ->with('i')
        ->willReturn($minute);

        $espeak = new EspeakVO($this->anything());
        $espeakEvent = new EspeakEvent($espeak);

        $this->mockEventDispatcher
        ->expects($this->at(0))
        ->method('dispatchInBackground')
        ->with($this->isInstanceOf(EspeakEvent::class), 0);

        $now = 1000;
        $notify_time = 1001;

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->willReturn($now);

        $this->mockTime
        ->expects($this->once())
        ->method('strtotime')
        ->with(BlogPostNotifyListener::NOTIFY_TIME)
        ->willReturn($notify_time);

        $this->mockEventDispatcher
        ->expects($this->at(1))
        ->method('dispatchInBackground')
        ->with($this->isInstanceOf(EspeakEvent::class), $notify_time);

        $this->subject->handlePostEvent($event);
    }
}
