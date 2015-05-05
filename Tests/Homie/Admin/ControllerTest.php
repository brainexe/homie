<?php

namespace Tests\Homie\Admin;

use BrainExe\Core\Authentication\UserVO;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Admin\Controller;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers Homie\Admin\Controller
 */
class ControllerTest extends TestCase
{
    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var DatabaseUserProvider|MockObject
     */
    private $userProvider;

    public function setUp()
    {
        $this->userProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);

        $this->subject = new Controller($this->userProvider);
    }

    public function testIndex()
    {
        $request = new Request();

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
            ->willReturn(42)
            ->willReturn($userVo);

        $actualResult = $this->subject->index($request);

        $expectedResult = [
            'users' => [
                42 => [
                    'userId' => 'mockId',
                    'username' => 'mockName',
                    'roles' => 'mockRole',
                    'email' => 'mockEmail',
                    'hasOneTimToken' => 'mockToken',
                    'avatar' => 'mockAvatar'
                ]
            ],
            'rights' => [
                UserVO::ROLE_ADMIN,
                UserVO::ROLE_USER,
            ]
        ];

        $this->assertEquals($expectedResult, $actualResult);
    }
}
