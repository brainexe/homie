<?php

namespace Tests\Raspberry\Blog;

use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Util\Time;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Blog\BlogPostVO;
use Raspberry\Blog\Controller;
use Raspberry\Blog\Blog;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Raspberry\Blog\Controller
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var Blog|MockObject
     */
    private $blog;

    /**
     * @var DatabaseUserProvider|MockObject
     */
    private $userProvider;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->blog         = $this->getMock(Blog::class, [], [], '', false);
        $this->userProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
        $this->time         = $this->getMock(Time::class, [], [], '', false);

        $this->subject = new Controller($this->blog, $this->userProvider);
        $this->subject->setTime($this->time);
    }

    public function testIndex()
    {
        $request = new Request();
        $userId = 42;

        $request->attributes->set('user_id', $userId);

        $posts = [];
        $userNames = [
                'Hans Peter' => $userId
        ];

        $this->blog
            ->expects($this->once())
            ->method('getPosts')
            ->with($userId)
            ->willReturn($posts);

        $this->userProvider
            ->expects($this->once())
            ->method('getAllUserNames')
            ->willReturn($userNames);

        $actualResult = $this->subject->index($request);

        $expectedResult = [
                'posts' => $posts,
                'users' => $userNames,
                'active_user_id' => $userId,
                'current_user_id' => $userId,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetMood()
    {
        $request = new Request();
        $userId  = 42;
        $recentPost = new BlogPostVO();
        $recentPost->mood = $mood = 3;
        $recentPost->text = $text = 'text';

        $request->attributes->set('user_id', $userId);

        $this->blog
            ->expects($this->once())
            ->method('getRecentPost')
            ->with($userId)
            ->willReturn($recentPost);

        $actualResult = $this->subject->getMood($request);

        $expectedResult = [
                'mood' => $mood * 10,
                'thought' => $text,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testBlogForUser()
    {
        $request = new Request();
        $userId = 42;

        $request->attributes->set('user_id', $userId);

        $posts = [];
        $userNames = [
                'Hans Peter' => $userId
        ];

        $this->blog
            ->expects($this->once())
            ->method('getPosts')
            ->with($userId)
            ->willReturn($posts);

        $this->userProvider
            ->expects($this->once())
            ->method('getAllUserNames')
            ->willReturn($userNames);

        $actualResult = $this->subject->blogForUser($request, $userId);

        $expectedResult = [
                'posts' => $posts,
                'users' => $userNames,
                'active_user_id' => $userId,
                'current_user_id' => $userId,
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @expectedException \BrainExe\Core\Application\UserException
     * @expectedExceptionMessage User not found: 42
     */
    public function testBlogForUserWithUndefinedUser()
    {
        $request = new Request();
        $userId = 42;

        $request->attributes->set('user_id', $userId);

        $posts = [];
        $userNames = [
                'Hans Peter' => 100
        ];

        $this->blog
            ->expects($this->once())
            ->method('getPosts')
            ->with($userId)
            ->willReturn($posts);

        $this->userProvider
            ->expects($this->once())
            ->method('getAllUserNames')
            ->willReturn($userNames);

        $actualResult = $this->subject->blogForUser($request, $userId);

        $this->markTestIncomplete();
    }

    public function testAddPost()
    {
        $request = new Request();
        $userId  = 42;
        $now     = 1000;
        $mood    = 8;
        $text    = 'text';

        $userVo = new UserVO();
        $userVo->id = $userId;

        $request->attributes->set('user', $userVo);
        $request->request->set('mood', $mood);
        $request->request->set('text', $text);

        $postVo = new BlogPostVO();
        $postVo->mood = $mood;
        $postVo->text = $text;

        $this->blog
            ->expects($this->once())
            ->method('addPost')
            ->with($userVo, $postVo)
            ->willReturn($postVo);

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn($now);

        $actualResult = $this->subject->addPost($request);

        $expectedResult = [
            $now,
            $postVo
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testDeletePost()
    {
        $userId = 42;
        $timestamp = 1212;

        $request = new Request();
        $request->attributes->set('user_id', $userId);

        $this->blog
            ->expects($this->once())
            ->method('deletePost')
            ->with($userId, $timestamp);

        $actualResult = $this->subject->deletePost($request, $timestamp);

        $expected_response = true;
        $this->assertEquals($expected_response, $actualResult);
    }
}
