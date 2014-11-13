<?php

namespace Tests\Raspberry\Blog\BlogPostNotifyListener;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Blog\BlogPostNotifyListener;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\Blog\BlogPostVO;
use Raspberry\Blog\Events\BlogEvent;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;

class BlogPostNotifyListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var BlogPostNotifyListener
	 */
	private $_subject;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	/**
	 * @var Time|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTime;

	public function setUp() {
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_mockTime = $this->getMock(Time::class, [], [], '', false);

		$this->_subject = new BlogPostNotifyListener();
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
		$this->_subject->setTime($this->_mockTime);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandlePostEvent() {
		$user_vo = new UserVO();
		$post_vo = new BlogPostVO();
		$post_vo->mood = $mood = 10;

		$event = new BlogEvent($user_vo, $post_vo);

		$hour = 12;
		$minute = 50;

		$this->_mockTime
			->expects($this->at(0))
			->method('date')
			->with('G')
			->will($this->returnValue($hour));

		$this->_mockTime
			->expects($this->at(1))
			->method('date')
			->with('i')
			->will($this->returnValue($minute));

		$espeak = new EspeakVO($this->anything());
		$espeak_event = new EspeakEvent($espeak);

		$this->_mockEventDispatcher
			->expects($this->at(0))
			->method('dispatchInBackground')
			->with($this->isInstanceOf(EspeakEvent::class), 0);

		$now = 1000;
		$notify_time = 1001;

		$this->_mockTime
			->expects($this->once())
			->method('now')
			->will($this->returnValue($now));

		$this->_mockTime
			->expects($this->once())
			->method('strtotime')
			->with(BlogPostNotifyListener::NOTIFY_TIME)
			->will($this->returnValue($notify_time));

		$this->_mockEventDispatcher
			->expects($this->at(1))
			->method('dispatchInBackground')
			->with($this->isInstanceOf(EspeakEvent::class), $notify_time);

		$this->_subject->handlePostEvent($event);
	}

}
