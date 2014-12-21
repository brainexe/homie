<?php

namespace Tests\Raspberry\Blog\BlogGateway;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Blog\BlogGateway;
use Raspberry\Blog\BlogPostVO;
use BrainExe\Core\Redis\Redis;

/**
 * @Covers Raspberry\Blog\BlogGateway
 */
class BlogGatewayTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var BlogGateway
     */
    private $subject;

    /**
     * @var Redis|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getMock(Redis::class, [], [], '', false);
        $this->subject = new BlogGateway();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testGetPosts()
    {
        $userId  = 42;
        $from    = 10;
        $to      = 100;

        $key = "blog:$userId";

        $post = new BlogPostVO();
        $posts_raw = [
        serialize($post) => $timestamp = 50
        ];

        $this->mockRedis
            ->expects($this->once())
            ->method('zRangeByScore')
            ->with($key, $from, $to, ['withscores' => true])
            ->will($this->returnValue($posts_raw));

        $actualResult = $this->subject->getPosts($userId, $from, $to);

        $expectedResult = [
         $timestamp => $post
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetRecentPostWithEmptyResult()
    {
        $userId = 0;

        $key = "blog:$userId";

        $postsRaw = [];

        $this->mockRedis
            ->expects($this->once())
            ->method('zRevRangeByScore')
            ->with($key, '+inf', '0', ['limit' => [0, 1]])
            ->will($this->returnValue($postsRaw));

        $actualResult = $this->subject->getRecentPost($userId);

        $this->assertNull($actualResult);
    }

    public function testGetRecentPost()
    {
        $userId = 0;

        $key = "blog:$userId";

        $post = new BlogPostVO();
        $posts_raw = [
        serialize($post)
        ];

        $this->mockRedis
        ->expects($this->once())
        ->method('zRevRangeByScore')
        ->with($key, '+inf', '0', ['limit' => [0, 1]])
        ->will($this->returnValue($posts_raw));

        $actualResult = $this->subject->getRecentPost($userId);

        $this->assertEquals($post, $actualResult);
    }

    public function testAddSubscriber()
    {
        $userId = 42;
        $targetId = 12;

        $this->mockRedis
        ->expects($this->once())
        ->method('sAdd')
        ->with("blog:subscribers:$targetId", $userId);

        $this->subject->addSubscriber($userId, $targetId);
    }

    public function testGetSubscriber()
    {
        $targetId = 12;

        $subscribers = [];

        $this->mockRedis
            ->expects($this->once())
            ->method('sMembers')
            ->with("blog:subscribers:$targetId")
            ->will($this->returnValue($subscribers));

        $actualResult = $this->subject->getSubscriber($targetId);

        $this->assertEquals($subscribers, $actualResult);
    }

    public function testAddPost()
    {
        $userId    = 42;
        $timestamp = 100;
        $postVo    = new BlogPostVO();

        $this->mockRedis
            ->expects($this->once())
            ->method('zAdd')
            ->with("blog:$userId", $timestamp, serialize($postVo));

        $this->subject->addPost($userId, $timestamp, $postVo);
    }

    public function testDeletePost()
    {
        $userId = 42;
        $timestamp = 1000;

        $this->mockRedis
        ->expects($this->once())
        ->method('zDeleteRangeByScore')
        ->with("blog:$userId");

        $this->subject->deletePost($userId, $timestamp);
    }
}
