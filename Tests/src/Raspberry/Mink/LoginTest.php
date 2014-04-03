<?php

namespace Raspberry\Tests\Mink;

use Matze\Core\EventDispatcher\BackgroundEvent;
use Matze\Core\EventDispatcher\DelayedEvent;
use Matze\Core\MessageQueue\MessageQueueGateway;
use Matze\Core\Util\TimeParser;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\RadioJob;
use Raspberry\Radio\VO\RadioVO;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LoginTest extends AbstractMinkTest {


	public function setup(){
		parent::setUp();
	}

	public function testNewUserShouldBeRedirectToLogin() {
		$this->markTestSkipped('Mink is not ready yet');
		
		$this->_mink->visit($this->_host);

		$expected_url = sprintf('%s/login/', $this->_host);
		$this->assertEquals($expected_url, $this->_mink->getCurrentUrl());
	}
} 
