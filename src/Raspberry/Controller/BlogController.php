<?php

namespace Raspberry\Controller;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Controller\AbstractController;
use Raspberry\Blog\Blog;
use Raspberry\Blog\BlogPostVO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class BlogController extends AbstractController {

	/**
	 * @var Blog
	 */
	private $_blog;
	/**
	 * @var DatabaseUserProvider
	 */
	private $_database_user_provider;

	/**
	 * @Inject({"@Blog", "@DatabaseUserProvider"})
	 * @param Blog $blog
	 * @param DatabaseUserProvider $database_user_provider
	 */
	public function __construct(Blog $blog, DatabaseUserProvider $database_user_provider) {
		$this->_blog = $blog;
		$this->_database_user_provider = $database_user_provider;
	}

	/**
	 * @param Request $request
	 * @return string
	 * @Route("/blog/", name="blog.index")
	 */
	public function index(Request $request) {
		$user_id = $request->attributes->get('user_id');

		return $this->blogForUser($request, $user_id);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/blog/mood/", name="blog.mood", methods="GET")
	 */
	public function getMood(Request $request) {
		$user_id = $request->attributes->get('user_id');

		$recent_post = $this->_blog->getRecentPost($user_id);

		return new JsonResponse([
			'mood' => $recent_post->mood * 10,
			'thought' => $recent_post->text,
		]);
	}

	/**
	 * @param Request $request
	 * @param integer $user_id
	 * @throws UserException
	 * @return JsonResponse
	 * @Route("/blog/{user_id}/", name="blog.user", methods="GET")
	 */
	public function blogForUser(Request $request, $user_id) {
		$current_user_id = $request->attributes->get('user_id');
		$posts = $this->_blog->getPosts($user_id);
		$users = $this->_database_user_provider->getAllUserNames();

		if (!in_array($user_id, $users)) {
			throw new UserException(sprintf('User not found: %s', $user_id));
		}

		return new JsonResponse([
			'posts' => $posts,
			'users' => $users,
			'active_user_id' => $user_id,
			'current_user_id' => $current_user_id,
		]);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @Route("/blog/add/", name="blog.add", methods="POST")
	 */
	public function addPost(Request $request) {
		$text = $request->request->get('text');
		$mood = $request->request->getInt('mood');

		/** @var UserVO $user */
		$user = $request->attributes->get('user');

		$blog_post_vo = new BlogPostVO();
		$blog_post_vo->text = $text;
		$blog_post_vo->mood = $mood;

		$this->_blog->addPost($user, $blog_post_vo);

		return new JsonResponse([
			 time(), $blog_post_vo
		]);
	}

	/**
	 * @param Request $request
	 * @param integer $timestamp
	 * @return JsonResponse
	 * @Route("/blog/delete/{timestamp}/", name="blog.delete", csrf=true)
	 */
	public function deletePost(Request $request, $timestamp) {
		$user_id = $request->attributes->get('user_id');

		$this->_blog->deletePost($user_id, $timestamp);

		return new JsonResponse(true);
	}

}
