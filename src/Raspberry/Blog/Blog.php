<?php

namespace Raspberry\Blog;

/**
 * @Service(public=false)
 */
class Blog {

	/**
	 * @var BlogGateway
	 */
	private $_blog_gateway;

	/**
	 * @Inject("@BlogGateway")
	 */
	public function __construct($blog_gateway) {
		$this->_blog_gateway = $blog_gateway;
	}

	/**
	 * @param integer $user_id
	 * @param string $from
	 * @param string $to
	 * @return BlogPostVO[]
	 */
	public function getPosts($user_id, $from = '0', $to = '+inf') {
		return $this->_blog_gateway->getPosts($user_id, $from, $to);
	}

	/**
	 * @param integer $user_id
	 * @param BlogPostVO $post_vo
	 */
	public function addPost($user_id, BlogPostVO $post_vo) {
		$this->_blog_gateway->addPost($user_id, time(), $post_vo);
	}

	/**
	 * @param integer $user_id
	 * @param integer $timestamp
	 */
	public function deletePost($user_id, $timestamp) {
		$this->_blog_gateway->deletePost($user_id, $timestamp);
	}
} 