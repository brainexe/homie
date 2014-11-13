<?php

namespace Tests\Raspberry\Client\LocalClient;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Client\LocalClient;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use RuntimeException;

/**
 * @Covers Raspberry\Client\LocalClient
 */
class LocalClientTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ProcessBuilder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockProcessBuilder;

	/**
	 * @var LocalClient
	 */
	private $_subject;

	public function setUp() {
		$this->_mockProcessBuilder = $this->getMock(ProcessBuilder::class, [], [], '', false);

		$this->_subject = new LocalClient($this->_mockProcessBuilder);
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage error
	 */
	public function testExecuteWithReturnAndFail() {
		$command = 'command';

		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->with([$command])
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setTimeout')
			->with(LocalClient::TIMEOUT)
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process
			->expects($this->once())
			->method('run');

		$process
			->expects($this->once())
			->method('isSuccessful')
			->will($this->returnValue(false));

		$process
			->expects($this->once())
			->method('getErrorOutput')
			->will($this->returnValue('error'));

		$this->_subject->executeWithReturn($command);
	}

	public function testExecuteWithReturn() {
		$command = 'command';
		$output  = 'output';
		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->with([$command])
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setTimeout')
			->with(LocalClient::TIMEOUT)
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process
			->expects($this->once())
			->method('run');

		$process
			->expects($this->once())
			->method('isSuccessful')
			->will($this->returnValue(true));

		$process
			->expects($this->once())
			->method('getOutput')
			->will($this->returnValue($output));

		$actual_result = $this->_subject->executeWithReturn($command);
		$this->assertEquals($output, $actual_result);
	}

	public function testExecute() {
		$command = 'command';
		$output  = 'output';
		$process = $this->getMock(Process::class, [], [], '', false);

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setArguments')
			->with([$command])
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('setTimeout')
			->with(LocalClient::TIMEOUT)
			->will($this->returnValue($this->_mockProcessBuilder));

		$this->_mockProcessBuilder
			->expects($this->once())
			->method('getProcess')
			->will($this->returnValue($process));

		$process
			->expects($this->once())
			->method('run');

		$process
			->expects($this->once())
			->method('isSuccessful')
			->will($this->returnValue(true));

		$process
			->expects($this->once())
			->method('getOutput')
			->will($this->returnValue($output));

		$this->_subject->execute($command);
	}

}
