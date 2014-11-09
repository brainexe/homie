<?php

namespace Tests\Raspberry\Blog\BlogPostNotifyListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Blog\BlogPostNotifyListener;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Blog\BlogPostNotifyListener
 */
class BlogPostNotifyListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var BlogPostNotifyListener
	 */
	private $_subject;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;


	public function setUp() {
		parent::setUp();

		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new BlogPostNotifyListener();
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandlePostEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->handlePostEvent($speak_event);
	}

}
