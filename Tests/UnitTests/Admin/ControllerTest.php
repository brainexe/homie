<?php

namespace Tests\Homie\Admin;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Admin\Controller;
use BrainExe\Core\Authentication\UserProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Homie\Admin\Controller
 */
class ControllerTest extends TestCase
{
    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var UserProvider|MockObject
     */
    private $userProvider;

    public function setUp()
    {
        $this->userProvider = $this->createMock(UserProvider::class);

        $this->subject = new Controller($this->userProvider);
    }

    public function testIndex()
    {
        $userVo = new UserVO();
        $userVo->id = 'mockId';
        $userVo->username = 'mockName';
        $userVo->roles = 'mockRole';
        $userVo->email = 'mockEmail';
        $userVo->one_time_secret = 'mockToken';
        $userVo->avatar = 'mockAvatar';

        $userIds = ['brain.exe' => 42];

        $this->userProvider
            ->expects($this->once())
            ->method('getAllUserNames')
            ->willReturn($userIds);

        $this->userProvider
            ->expects($this->once())
            ->method('loadUserById')
            ->with(42)
            ->willReturn($userVo);

        $actual = $this->subject->index();

        $expected = [
            'users' => [
                42 => [
                    'userId' => 'mockId',
                    'username' => 'mockName',
                    'roles' => 'mockRole',
                    'email' => 'mockEmail',
                    'avatar' => 'mockAvatar'
                ]
            ],
            'rights' => [
                UserVO::ROLE_ADMIN,
                UserVO::ROLE_USER,
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSave()
    {
        $request = new Request();
        $request->request->set('userId', $userId = 42);
        $request->request->set('email', $email = 'newEmail');
        $request->request->set('roles', $roles = ['newRoles']);
        $request->request->set('password', $password = 'newPassword');

        $userVo = new UserVO();

        $this->userProvider
            ->expects($this->at(0))
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($userVo);
        $this->userProvider
            ->expects($this->at(1))
            ->method('setUserProperty')
            ->with($userVo, 'email');
        $this->userProvider
            ->expects($this->at(2))
            ->method('setUserProperty')
            ->with($userVo, 'roles');
        $this->userProvider
            ->expects($this->at(3))
            ->method('changePassword')
            ->with($userVo, $password);

        $actualResult = $this->subject->save($request);

        $this->assertEquals($userVo, $actualResult);
    }

    public function testSaveUpdateTwoPropertiesOnly()
    {
        $request = new Request();
        $request->request->set('userId', $userId = 42);
        $request->request->set('email', $email = 'newEmail');
        $request->request->set('roles', $roles = ['newRoles']);
        $request->request->set('password', $password = 'newPassword');

        $userVo = new UserVO();
        $userVo->roles = $roles;

        $this->userProvider
            ->expects($this->at(0))
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($userVo);
        $this->userProvider
            ->expects($this->at(1))
            ->method('setUserProperty')
            ->with($userVo, 'email');
        $this->userProvider
            ->expects($this->at(2))
            ->method('changePassword')
            ->with($userVo, $password);

        $actualResult = $this->subject->save($request);

        $this->assertEquals($userVo, $actualResult);
    }

    public function testSaveUpdatePasswordOnly()
    {
        $request = new Request();
        $request->request->set('userId', $userId = 42);
        $request->request->set('email', $email = '');
        $request->request->set('roles', $roles = []);
        $request->request->set('password', $password = 'newPassword');

        $userVo = new UserVO();
        $userVo->roles = $roles;

        $this->userProvider
            ->expects($this->at(0))
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($userVo);
        $this->userProvider
            ->expects($this->at(1))
            ->method('changePassword')
            ->with($userVo, $password);

        $actualResult = $this->subject->save($request);

        $this->assertEquals($userVo, $actualResult);
    }

    public function testSaveDoNothing()
    {
        $request = new Request();
        $request->request->set('userId', $userId = 42);
        $request->request->set('email', $email = 'newEmail');
        $request->request->set('roles', $roles = ['newRoles']);
        $request->request->set('password', '');

        $userVo = new UserVO();
        $userVo->email = $email;
        $userVo->roles = $roles;

        $this->userProvider
            ->expects($this->at(0))
            ->method('loadUserById')
            ->with($userId)
            ->willReturn($userVo);
        $this->userProvider
            ->expects($this->never())
            ->method('setUserProperty');

        $actualResult = $this->subject->save($request);

        $this->assertEquals($userVo, $actualResult);
    }

    public function testDelete()
    {
        $request = new Request();
        $request->request->set('userId', $userId = 42);

        $this->userProvider
            ->expects($this->once())
            ->method('deleteUser')
            ->with($userId)
            ->willReturn(true);

        $actualResult = $this->subject->delete($request);

        $this->assertTrue($actualResult);
    }
}
