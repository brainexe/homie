<?php

namespace Tests\Raspberry\Client\DummyClient;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Client\DummyClient;
use Monolog\Logger;

/**
 * @Covers Raspberry\Client\DummyClient
 */
class DummyClientTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var DummyClient
	 */
	private $_subject;

	/**
	 * @var Logger|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockLogger;

	public function setUp() {
		$this->_mockLogger = $this->getMock(Logger::class, [], [], '', false);

		$this->_subject = new DummyClient();
		$this->_subject->setLogger($this->_mockLogger);
	}

	public function testExecute() {
		$command = 'test';

		$this->_mockLogger
			->expects($this->once())
			->method('log')
			->with('info', $command);

		$this->_subject->execute($command);
	}

	public function testExecuteWithReturn() {
		$command = 'test';

		$this->_mockLogger
			->expects($this->once())
			->method('log')
			->with('info', $command);

		$actual_result = $this->_subject->executeWithReturn($command);

		$this->assertEquals('', $actual_result);
	}

}
