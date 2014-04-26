<?php

namespace Raspberry\Controller;

use Matze\Core\Application\UserException;
use Matze\Core\Authentication\DatabaseUserProvider;
use Matze\Core\Authentication\UserVO;
use Matze\Core\Controller\AbstractController;
use Matze\Core\Traits\TwigTrait;
use Raspberry\Blog\Blog;
use Raspberry\Blog\BlogPostVO;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
		$user_id = $request->getSession()->get('user')->id;

		return $this->blogForUser($request, $user_id);
	}

	/**
	 * @param Request $request
	 * @param integer $user_id
	 * @throws UserException
	 * @return string
	 * @Route("/blog/{user_id}/", name="blog.user")
	 */
	public function blogForUser(Request $request, $user_id) {
		$current_user_id = $request->getSession()->get('user')->id;
		$posts = $this->_blog->getPosts($user_id);
		$users = $this->_database_user_provider->getAllUserNames();

		if (!in_array($user_id, $users)) {
			throw new UserException(sprintf('User not found: %s', $user_id));
		}

		return $this->render('blog.html.twig', [
			'posts' => $posts,
			'users' => $users,
			'active_user_id' => $user_id,
			'current_user_id' => $current_user_id,
		]);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @Route("/blog/add/", name="blog.add", methods="POST")
	 */
	public function addPost(Request $request) {
		$text = $request->request->get('text');
		$mood = $request->request->getInt('mood');

		/** @var UserVO $user */
		$user = $request->getSession()->get('user');

		$blog_post_vo = new BlogPostVO();
		$blog_post_vo->text = $text;
		$blog_post_vo->mood = $mood;

		$this->_blog->addPost($user, $blog_post_vo);

		return new RedirectResponse('/blog/');
	}

	/**
	 * @param Request $request
	 * @param integer $timestamp
	 * @return RedirectResponse
	 * @Route("/blog/delete/{timestamp}/", name="blog.delete", csrf=true)
	 */
	public function deletePost(Request $request, $timestamp) {
		$user_id = $request->getSession()->get('user')->id;

		$this->_blog->deletePost($user_id, $timestamp);

		return new RedirectResponse('/blog/');
	}

}
