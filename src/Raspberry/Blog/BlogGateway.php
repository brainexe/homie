<?php

namespace Raspberry\Blog;

use BrainExe\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class BlogGateway
{

    const REDIS_POSTS_KEY = 'blog:%d';
    const REDIS_SUBSCRIBERS = 'blog:subscribers:%d';

    use RedisTrait;

    /**
     * @param integer $userId
     * @param string $fromTime
     * @param string $toTime
     * @return BlogPostVO[]
     */
    public function getPosts($userId, $fromTime = '0', $toTime = '+inf')
    {
        $key = $this->getPostKey($userId);

        $posts = [];
        $postsRaw = $this->getRedis()->zRangeByScore($key, $fromTime, $toTime, ['withscores' => true]);

        foreach ($postsRaw as $serialized => $timestamp) {
            $posts[$timestamp] = unserialize($serialized);
        }

        return $posts;
    }

    /**
     * @param integer $userId
     * @return BlogPostVO|null
     */
    public function getRecentPost($userId)
    {
        $key = $this->getPostKey($userId);

        $raw = $this->getRedis()->zRevRangeByScore($key, '+inf', '0', ['limit' => [0, 1]]);

        if (empty($raw)) {
            return null;
        }

        return unserialize($raw[0]);
    }

    /**
     * @param integer $userId
     * @param integer $targetId
     */
    public function addSubscriber($userId, $targetId)
    {
        $key = $this->getSubscriberKey($targetId);
        $this->getRedis()->sAdd($key, $userId);
    }

    /**
     * @param integer $targetId
     * @return integer[]
     */
    public function getSubscriber($targetId)
    {
        $key = $this->getSubscriberKey($targetId);

        return $this->getRedis()->sMembers($key);
    }

    /**
     * @param integer $userId
     * @param integer $timestamp
     * @param BlogPostVO $postVo
     */
    public function addPost($userId, $timestamp, BlogPostVO $postVo)
    {
        $key = $this->getPostKey($userId);

        $this->getRedis()->zAdd($key, $timestamp, serialize($postVo));
    }

    /**
     * @param integer $userId
     * @param integer $timestamp
     */
    public function deletePost($userId, $timestamp)
    {
        $key = $this->getPostKey($userId);

        $this->getRedis()->zDeleteRangeByScore($key, $timestamp, $timestamp);
    }

    /**
     * @param integer $userId
     * @return string
     */
    private function getSubscriberKey($userId)
    {
        return sprintf(self::REDIS_SUBSCRIBERS, $userId);
    }

    /**
     * @param integer $userId
     * @return string
     */
    private function getPostKey($userId)
    {
        return sprintf(self::REDIS_POSTS_KEY, $userId);
    }
}
