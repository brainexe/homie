<?php

namespace Tests\Raspberry\Media\Sound;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Media\Sound;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @Covers Raspberry\Media\Sound
 */
class SoundTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var Sound
	 */
	private $_subject;

	/**
	 * @var ProcessBuilder|MockObject
	 */
	private $_mockProcessBuilder;

	public function setUp() {
		$this->_mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);
		$this->_subject = new Sound($this->_mockProcessBuilder);
	}

	public function testPlaySound() {
		$file = 'file';

		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->with([Sound::COMMAND, $file])
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process
			->expects($this->once())
			->method('run');

		$this->_subject->playSound($file);
	}

}
