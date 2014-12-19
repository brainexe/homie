<?php

namespace Raspberry\Controller;

use BrainExe\Core\Application\UserException;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use BrainExe\Core\Authentication\UserVO;
use BrainExe\Core\Controller\ControllerInterface;
use BrainExe\Core\Traits\TimeTrait;
use Raspberry\Blog\Blog;
use Raspberry\Blog\BlogPostVO;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Controller
 */
class BlogController implements ControllerInterface
{

    use TimeTrait;

    /**
     * @var Blog
     */
    private $blog;

    /**
     * @var DatabaseUserProvider
     */
    private $userProvider;

    /**
     * @Inject({"@Blog", "@DatabaseUserProvider"})
     * @param Blog $blog
     * @param DatabaseUserProvider $userProvider
     */
    public function __construct(Blog $blog, DatabaseUserProvider $userProvider)
    {
        $this->blog                   = $blog;
        $this->userProvider = $userProvider;
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/blog/", name="blog.index")
     */
    public function index(Request $request)
    {
        $userId = $request->attributes->get('user_id');

        return $this->blogForUser($request, $userId);
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/blog/mood/", name="blog.mood", methods="GET")
     */
    public function getMood(Request $request)
    {
        $user = $request->attributes->get('user_id');

        $recentPost = $this->blog->getRecentPost($user);

        return [
        'mood' => $recentPost->mood * 10,
        'thought' => $recentPost->text,
        ];
    }

    /**
     * @param Request $request
     * @param integer $userId
     * @throws UserException
     * @return array
     * @Route("/blog/{user_id}/", name="blog.user", methods="GET")
     */
    public function blogForUser(Request $request, $userId)
    {
        $currentUserId = $request->attributes->get('user_id');
        $posts           = $this->blog->getPosts($userId);
        $users           = $this->userProvider->getAllUserNames();

        if (!in_array($userId, $users)) {
            throw new UserException(sprintf('User not found: %s', $userId));
        }

        return [
        'posts' => $posts,
        'users' => $users,
        'active_user_id' => $userId,
        'current_user_id' => $currentUserId,
        ];
    }

    /**
     * @param Request $request
     * @return array
     * @Route("/blog/add/", name="blog.add", methods="POST")
     */
    public function addPost(Request $request)
    {
        $text = $request->request->get('text');
        $mood = $request->request->getInt('mood');

        /** @var UserVO $user */
        $user = $request->attributes->get('user');

        $post       = new BlogPostVO();
        $post->text = $text;
        $post->mood = $mood;

        $this->blog->addPost($user, $post);

        return [
            $this->now(),
            $post
        ];
    }

    /**
     * @param Request $request
     * @param integer $timestamp
     * @return boolean
     * @Route("/blog/delete/{timestamp}/", name="blog.delete", csrf=true)
     */
    public function deletePost(Request $request, $timestamp)
    {
        $user = $request->attributes->get('user_id');

        $this->blog->deletePost($user, $timestamp);

        return true;
    }
}
