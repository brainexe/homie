<?php

namespace Raspberry\Controller;

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
	 * @Inject("@Blog")
	 */
	public function __construct(Blog $blog) {
		$this->_blog = $blog;
	}

	/**
	 * @param Request $request
	 * @return string
	 * @Route("/blog/", name="blog.index")
	 */
	public function index(Request $request) {
		$user_id = $request->getSession()->get('user')->id;

		$posts = $this->_blog->getPosts($user_id);

		return $this->render('blog.html.twig', [
			'posts' => $posts,
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
	 * @Route("/blog/delete/{timestamp}/", name="blog.delete")
	 */
	public function deletePost(Request $request, $timestamp) {
		$user_id = $request->getSession()->get('user')->id;

		$this->_blog->deletePost($user_id, $timestamp);

		return new RedirectResponse('/blog/');
	}

}
