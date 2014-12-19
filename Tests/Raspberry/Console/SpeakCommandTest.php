<?php

namespace Tests\Raspberry\Console\SpeakCommand;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Console\SpeakCommand;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\Console\SpeakCommand
 */
class SpeakCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SpeakCommand
	 */
	private $_subject;

	/**
	 * @var EventDispatcher|MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_subject = new SpeakCommand();
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testExecute() {
		$text = 'nice text';

		$application = new Application();
		$application->add($this->_subject);

		$commandTester = new CommandTester($this->_subject);

		$espeak_vo = new EspeakVO($text);
		$event = new EspeakEvent($espeak_vo);

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchEvent')
			->with($event);

		$commandTester->execute(['text' => $text]);
	}
}
