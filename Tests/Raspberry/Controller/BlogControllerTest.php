<?php

namespace Tests\Raspberry\Controller\BlogController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\Controller\BlogController;
use Raspberry\Blog\Blog;
use BrainExe\Core\Authentication\DatabaseUserProvider;

/**
 * @Covers Raspberry\Controller\BlogController
 */
class BlogControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var BlogController
	 */
	private $_subject;

	/**
	 * @var Blog|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockBlog;

	/**
	 * @var DatabaseUserProvider|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDatabaseUserProvider;


	public function setUp() {
		parent::setUp();

		$this->_mockBlog = $this->getMock(Blog::class, [], [], '', false);
		$this->_mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

		$this->_subject = new BlogController($this->_mockBlog, $this->_mockDatabaseUserProvider);

	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->index($request);
	}

	public function testGetMood() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getMood($request);
	}

	public function testBlogForUser() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->blogForUser($request, $user_id);
	}

	public function testAddPost() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addPost($request);
	}

	public function testDeletePost() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->deletePost($request, $timestamp);
	}

}
