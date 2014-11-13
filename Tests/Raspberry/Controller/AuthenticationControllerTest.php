<?php

namespace Tests\Raspberry\Controller\AuthenticationController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\AuthenticationController;
use BrainExe\Core\DependencyInjection\ObjectFinder;

/**
 * @Covers Raspberry\Controller\AuthenticationController
 */
class AuthenticationControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var AuthenticationController
	 */
	private $_subject;

	/**
	 * @var ObjectFinder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockObjectFinder;

	public function setUp() {

		$this->_mockObjectFinder = $this->getMock(ObjectFinder::class, [], [], '', false);
		$this->_subject = new AuthenticationController();
		$this->_subject->setObjectFinder($this->_mockObjectFinder);
	}

	public function testIncomplete() {
		$this->markTestIncomplete('This is only a dummy implementation');
	}

}
