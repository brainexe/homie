<?php

namespace Homie\Admin;

use BrainExe\Core\Annotations\Controller as ControllerAnnotation;
use BrainExe\Core\Annotations\Role;
use BrainExe\Core\Annotations\Route;
use BrainExe\Core\Authentication\UserProvider;
use BrainExe\Core\Authentication\UserVO;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ControllerAnnotation
 */
class Controller
{

    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * @param UserProvider $userProvider
     */
    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @return array
     * @Route("/admin/users/", name="admin.users", methods="GET")
     * @Role(UserVO::ROLE_ADMIN)
     */
    public function index() : array
    {
        $userIds = $this->userProvider->getAllUserNames();
        $users = [];

        foreach ($userIds as $id) {
            $user = $this->userProvider->loadUserById($id);
            $users[$id] = $user->toArray();
        }

        return [
            'users'  => $users,
            'rights' => UserVO::ROLES
        ];
    }

    /**
     * @param Request $request
     * @return UserVO
     * @Route("/admin/users/", name="admin.users.save", methods="PUT")
     * @Role(UserVO::ROLE_ADMIN)
     */
    public function save(Request $request) : UserVO
    {
        $userId    = $request->request->getInt('userId');
        $email     = $request->request->get('email');
        $roles     = $request->request->get('roles');
        $password  = (string)$request->request->get('password');

        $user = $this->userProvider->loadUserById($userId);

        $this->changeEmail($email, $user);
        $this->changeRoles($roles, $user);
        $this->changePassword($password, $user);

        return $user;
    }

    /**
     * @param Request $request
     * @return bool
     * @Route("/admin/users/delete/", name="admin.user.delete")
     * @Role(UserVO::ROLE_ADMIN)
     */
    public function delete(Request $request) : bool
    {
        $userId = $request->request->getInt('userId');

        return $this->userProvider->deleteUser($userId);
    }

    /**
     * @param string $email
     * @param UserVO $user
     */
    private function changeEmail(string  $email, UserVO $user) : void
    {
        if ($email && $email !== $user->email) {
            $user->email = $email;
            $this->userProvider->setUserProperty($user, 'email');
        }
    }

    /**
     * @param $roles
     * @param UserVO $user
     */
    private function changeRoles($roles, UserVO $user) : void
    {
        if ($roles && $roles !== $user->roles) {
            $user->roles = $roles;
            $this->userProvider->setUserProperty($user, 'roles');
        }
    }

    /**
     * @param string $password
     * @param UserVO $user
     */
    private function changePassword(string $password, UserVO $user) : void
    {
        if ($password) {
            $this->userProvider->changePassword($user, $password);
        }
    }
}
