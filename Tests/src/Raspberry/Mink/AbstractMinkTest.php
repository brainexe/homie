<?php

namespace Raspberry\Tests\Mink;

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Session;

abstract class AbstractMinkTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Session
	 */
	protected $_mink;

	/**
	 * @var string
	 */
	protected $_host;

	function setUp() {
		$this->_host = 'http://localhost:8080';
		$driver = new GoutteDriver();

		$this->_mink = new Session($driver);

		$this->_mink->start();
	}
} 