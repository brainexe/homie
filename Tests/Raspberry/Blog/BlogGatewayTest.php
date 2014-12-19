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
        $user_id = 42;
        $from    = 10;
        $to      = 100;

        $key = "blog:$user_id";

        $post = new BlogPostVO();
        $posts_raw = [
        serialize($post) => $timestamp = 50
        ];

        $this->mockRedis
        ->expects($this->once())
        ->method('zRangeByScore')
        ->with($key, $from, $to, ['withscores' => true])
        ->will($this->returnValue($posts_raw));

        $actual_result = $this->subject->getPosts($user_id, $from, $to);

        $expected_result = [
        $timestamp => $post
        ];

        $this->assertEquals($expected_result, $actual_result);
    }

    public function testGetRecentPostWithEmptyResult()
    {
        $user_id = 0;

        $key = "blog:$user_id";

        $posts_raw = [];

        $this->mockRedis
        ->expects($this->once())
        ->method('zRevRangeByScore')
        ->with($key, '+inf', '0', ['limit' => [0, 1]])
        ->will($this->returnValue($posts_raw));

        $actual_result = $this->subject->getRecentPost($user_id);

        $this->assertNull($actual_result);
    }

    public function testGetRecentPost()
    {
        $user_id = 0;

        $key = "blog:$user_id";

        $post = new BlogPostVO();
        $posts_raw = [
        serialize($post)
        ];

        $this->mockRedis
        ->expects($this->once())
        ->method('zRevRangeByScore')
        ->with($key, '+inf', '0', ['limit' => [0, 1]])
        ->will($this->returnValue($posts_raw));

        $actual_result = $this->subject->getRecentPost($user_id);

        $this->assertEquals($post, $actual_result);
    }

    public function testAddSubscriber()
    {
        $user_id = 42;
        $target_id = 12;

        $this->mockRedis
        ->expects($this->once())
        ->method('sAdd')
        ->with("blog:subscribers:$target_id", $user_id);

        $this->subject->addSubscriber($user_id, $target_id);
    }

    public function testGetSubscriber()
    {
        $target_id = 12;

        $subscribers = [];

        $this->mockRedis
        ->expects($this->once())
        ->method('sMembers')
        ->with("blog:subscribers:$target_id")
        ->will($this->returnValue($subscribers));

        $actual_result = $this->subject->getSubscriber($target_id);

        $this->assertEquals($subscribers, $actual_result);
    }

    public function testAddPost()
    {
        $user_id = 42;
        $timestamp = 100;
        $post_vo = new BlogPostVO();

        $this->mockRedis
        ->expects($this->once())
        ->method('zAdd')
        ->with("blog:$user_id", $timestamp, serialize($post_vo));

        $this->subject->addPost($user_id, $timestamp, $post_vo);
    }

    public function testDeletePost()
    {
        $user_id = 42;
        $timestamp = 1000;

        $this->mockRedis
        ->expects($this->once())
        ->method('zDeleteRangeByScore')
        ->with("blog:$user_id");

        $this->subject->deletePost($user_id, $timestamp);
    }
}
