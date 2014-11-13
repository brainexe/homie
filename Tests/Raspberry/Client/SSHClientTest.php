<?php

namespace Tests\Raspberry\Client\SSHClient;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Client\SSHClient;

/**
 * @Covers Raspberry\Client\SSHClient
 */
class SSHClientTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SSHClient
	 */
	private $_subject;

	public function setUp() {
		$this->_subject = new SSHClient();
	}

	public function testExecute() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->execute($command);
	}

	public function testExecuteWithReturn() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->executeWithReturn($command);
	}

}
