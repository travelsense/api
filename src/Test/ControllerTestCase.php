<?php
namespace Test;


class ControllerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildUser()
    {
        $user = $this->getMock(
            'Model\\User',
            ['getEmail', 'getPicture', 'getFirstName', 'getLastName', 'getId']
        );
        $user->method('getEmail')->willReturn('user1@example.com');
        $user->method('getPicture')->willReturn('http://example.com/user1.jpg');
        $user->method('getFirstName')->willReturn('User1');
        $user->method('getLastName')->willReturn('Tester');
        $user->method('getId')->willReturn(1);
        return $user;
    }

}