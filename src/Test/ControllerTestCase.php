<?php
namespace Api\Test;

use DateTime;
use PHPUnit_Framework_TestCase;

class ControllerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildUser()
    {
        $user = $this->getMock(
            'Api\\Model\\User',
            ['getEmail', 'getPicture', 'getFirstName', 'getLastName', 'getId', 'getCreated']
        );
        $user->method('getEmail')->willReturn('user1@example.com');
        $user->method('getPicture')->willReturn('http://example.com/user1.jpg');
        $user->method('getFirstName')->willReturn('User1');
        $user->method('getLastName')->willReturn('Tester');
        $user->method('getId')->willReturn(1);
        $user->method('getCreated')->willReturn(new DateTime('2000-01-01'));
        return $user;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildCategory()
    {
        $category = $this->getMock(
            'Api\\Model\\Travel\\Category',
            ['getId', 'getName']
        );
        $category->method('getId')->willReturn(1);
        $category->method('getName')->willReturn('test_category');
        return $category;
    }
}
