<?php

namespace Raspberry\Blog;
use Matze\Core\Traits\RedisTrait;

/**
 * @Service(public=false)
 */
class BlogGateway {
	const REDIS_POSTS_KEY = 'blog:%d';
	const REDIS_SUBSCRIBERS = 'blog:subscribers:%d';

	use RedisTrait;

	/**
	 * @param integer $user_id
	 * @param string $from
	 * @param string $to
	 * @return BlogPostVO[]
	 */
	public function getPosts($user_id, $from = '0', $to = '+inf') {
		$key = $this->_getPostKey($user_id);

		$posts = [];
		$posts_raw = $this->getRedis()->zRangeByScore($key, $from, $to, ['withscores' => true]);

		foreach ($posts_raw as $serialized => $timestamp) {
			$posts[$timestamp] = unserialize($serialized);
		}

		return $posts;
	}

	/**
	 * @param integer $user_id
	 * @param integer $target_id
	 */
	public function addSubscriber($user_id, $target_id) {
		$key = $this->_getSubscriberKey($target_id);
		$this->getRedis()->sAdd($key, $user_id);
	}

	/**
	 * @param integer $target_id
	 * @return integer[]
	 */
	public function getSubscriber($target_id) {
		$key = $this->_getSubscriberKey($target_id);

		return $this->getRedis()->sMembers($key);
	}

	/**
	 * @param integer $user_id
	 * @param integer $timestamp
	 * @param BlogPostVO $post_vo
	 */
	public function addPost($user_id, $timestamp, BlogPostVO $post_vo) {
		$key = $this->_getPostKey($user_id);
		$this->getRedis()->zAdd($key, $timestamp, serialize($post_vo));
	}

	/**
	 * @param integer $user_id
	 * @param integer $timestamp
	 */
	public function deletePost($user_id, $timestamp) {
		$key = $this->_getPostKey($user_id);

		$this->getRedis()->zDeleteRangeByScore($key, $timestamp, $timestamp);
	}

	/**
	 * @param integer $user_id
	 * @return string
	 */
	private function _getSubscriberKey($user_id) {
		return sprintf(self::REDIS_SUBSCRIBERS, $user_id);
	}

	/**
	 * @param integer $user_id
	 * @return string
	 */
	private function _getPostKey($user_id) {
		return sprintf(self::REDIS_POSTS_KEY, $user_id);
	}

} 