<?php

namespace Raspberry\Blog;
use Matze\Core\Authentication\UserVO;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Blog\Events\BlogEvent;

/**
 * @Service(public=false)
 */
class Blog {

	use EventDispatcherTrait;

	/**
	 * @var BlogGateway
	 */
	private $_blog_gateway;

	/**
	 * @Inject("@BlogGateway")
	 */
	public function __construct(BlogGateway $blog_gateway) {
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
	 * @param UserVO $user
	 * @param BlogPostVO $post_vo
	 */
	public function addPost(UserVO $user, BlogPostVO $post_vo) {
		if ($post_vo->mood < BlogPostVO::MIN_MOOD || $post_vo->mood > BlogPostVO::MAX_MOOD) {
			$post_vo->mood = null;
		}

		$this->_blog_gateway->addPost($user->id, time(), $post_vo);

		$event = new BlogEvent($user, $post_vo);
		$this->dispatchInBackground($event);
	}

	/**
	 * @param integer $user_id
	 * @param integer $timestamp
	 */
	public function deletePost($user_id, $timestamp) {
		$this->_blog_gateway->deletePost($user_id, $timestamp);
	}

	/**
	 * @param integer $user_id
	 * @param integer $target_id
	 */
	public function addSubscriber($user_id, $target_id) {
		$this->_blog_gateway->addSubscriber($user_id, $target_id);
	}

} 