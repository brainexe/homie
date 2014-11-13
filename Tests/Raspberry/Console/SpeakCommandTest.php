<?php

namespace Tests\Raspberry\Console\SpeakCommand;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Console\SpeakCommand;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\Console\SpeakCommand
 */
class SpeakCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SpeakCommand
	 */
	private $_subject;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_subject = new SpeakCommand();
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testIncomplete() {
		$this->markTestIncomplete('This is only a dummy implementation');
	}
}
