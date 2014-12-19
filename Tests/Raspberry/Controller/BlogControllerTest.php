<?php

namespace Tests\Raspberry\Controller\BlogController;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Blog\BlogPostVO;
use Raspberry\Controller\BlogController;
use Raspberry\Blog\Blog;
use BrainExe\Core\Authentication\DatabaseUserProvider;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Covers Raspberry\Controller\BlogController
 */
class BlogControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var BlogController
     */
    private $subject;

    /**
     * @var Blog|MockObject
     */
    private $mockBlog;

    /**
     * @var DatabaseUserProvider|MockObject
     */
    private $mockDatabaseUserProvider;

    /**
     * @var Time|MockObject
     */
    private $mockTime;

    public function setUp()
    {
        $this->mockBlog = $this->getMock(Blog::class, [], [], '', false);
        $this->mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
        $this->mockTime = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new BlogController($this->mockBlog, $this->mockDatabaseUserProvider);
        $this->subject->setTime($this->mockTime);
    }

    public function testIndex()
    {
        $request = new Request();
        $user_id = 42;

        $request->attributes->set('user_id', $user_id);

        $posts = [];
        $user_names = [
        'Hans Peter' => $user_id
        ];

        $this->mockBlog
        ->expects($this->once())
        ->method('getPosts')
        ->with($user_id)
        ->will($this->returnValue($posts));

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('getAllUserNames')
        ->will($this->returnValue($user_names));

        $actual_result = $this->subject->index($request);

        $expected_result = [
        'posts' => $posts,
        'users' => $user_names,
        'active_user_id' => $user_id,
        'current_user_id' => $user_id,
        ];

        $this->assertEquals($expected_result, $actual_result);
    }

    public function testGetMood()
    {
        $request = new Request();
        $user_id = 42;
        $recent_post = new BlogPostVO();
        $recent_post->mood = $mood = 3;
        $recent_post->text = $text = 'text';

        $request->attributes->set('user_id', $user_id);

        $this->mockBlog
        ->expects($this->once())
        ->method('getRecentPost')
        ->with($user_id)
        ->will($this->returnValue($recent_post));

        $actual_result = $this->subject->getMood($request);

        $expected_result = [
        'mood' => $mood * 10,
        'thought' => $text,
        ];

        $this->assertEquals($expected_result, $actual_result);
    }

    public function testBlogForUser()
    {
        $request = new Request();
        $user_id = 42;

        $request->attributes->set('user_id', $user_id);

        $posts = [];
        $user_names = [
        'Hans Peter' => $user_id
        ];

        $this->mockBlog
        ->expects($this->once())
        ->method('getPosts')
        ->with($user_id)
        ->will($this->returnValue($posts));

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('getAllUserNames')
        ->will($this->returnValue($user_names));

        $actual_result = $this->subject->blogForUser($request, $user_id);

        $expected_result = [
        'posts' => $posts,
        'users' => $user_names,
        'active_user_id' => $user_id,
        'current_user_id' => $user_id,
        ];

        $this->assertEquals($expected_result, $actual_result);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage User not found: 42
     */
    public function testBlogForUserWithUndefinedUser()
    {
        $request = new Request();
        $user_id = 42;

        $request->attributes->set('user_id', $user_id);

        $posts = [];
        $user_names = [
        'Hans Peter' => 100
        ];

        $this->mockBlog
        ->expects($this->once())
        ->method('getPosts')
        ->with($user_id)
        ->will($this->returnValue($posts));

        $this->mockDatabaseUserProvider
        ->expects($this->once())
        ->method('getAllUserNames')
        ->will($this->returnValue($user_names));

        $actual_result = $this->subject->blogForUser($request, $user_id);

        $i = $todo;
    }

    public function testAddPost()
    {
        $request = new Request();
        $user_id = 42;
        $now = 1000;
        $mood = 8;
        $text = 'text';

        $user_vo = new UserVO();
        $user_vo->id = $user_id;

        $request->attributes->set('user', $user_vo);
        $request->request->set('mood', $mood);
        $request->request->set('text', $text);

        $post_vo = new BlogPostVO();
        $post_vo->mood = $mood;
        $post_vo->text = $text;

        $this->mockBlog
        ->expects($this->once())
        ->method('addPost')
        ->with($user_vo, $post_vo)
        ->will($this->returnValue($post_vo));

        $this->mockTime
        ->expects($this->once())
        ->method('now')
        ->will($this->returnValue($now));

        $actual_result = $this->subject->addPost($request);

        $expected_result = [
        $now, $post_vo
        ];

        $this->assertEquals($expected_result, $actual_result);
    }

    public function testDeletePost()
    {
        $user_id = 42;
        $timestamp = 1212;

        $request = new Request();
        $request->attributes->set('user_id', $user_id);

        $this->mockBlog
        ->expects($this->once())
        ->method('deletePost')
        ->with($user_id, $timestamp);

        $actual_result = $this->subject->deletePost($request, $timestamp);

        $expected_response = true;
        $this->assertEquals($expected_response, $actual_result);
    }
}
