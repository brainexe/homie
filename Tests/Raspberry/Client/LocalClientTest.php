<?php

namespace Tests\Raspberry\Client\LocalClient;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Client\LocalClient;

/**
 * @Covers Raspberry\Client\LocalClient
 */
class LocalClientTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var LocalClient
	 */
	private $_subject;



	public function setUp() {
		parent::setUp();



		$this->_subject = new LocalClient();

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
