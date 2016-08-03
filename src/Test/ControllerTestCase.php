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
        $user = $this->getMockBuilder('Api\\Model\\User')
            ->setMethods(['getEmail', 'getPicture', 'getFirstName', 'getLastName', 'getId', 'isCreator', 'getCreated'])
            ->getMock();
        $user->method('getEmail')->willReturn('user1@example.com');
        $user->method('getPicture')->willReturn('http://example.com/user1.jpg');
        $user->method('getFirstName')->willReturn('User1');
        $user->method('getLastName')->willReturn('Tester');
        $user->method('getId')->willReturn(1);
        $user->method('isCreator')->willReturn(false);
        $user->method('getCreated')->willReturn(new DateTime('2000-01-01'));
        return $user;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildCategory()
    {
        $category = $this->getMockBuilder('Api\\Model\\Travel\\Category')
            ->setMethods(['getId', 'getName'])
            ->getMock();
        $category->method('getId')->willReturn(1);
        $category->method('getName')->willReturn('test_category');
        return $category;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function buildTravel()
    {
        $travel = $this->getMockBuilder('Api\\Model\\Travel\\Travel')
            ->setMethods(['getId', 'getTitle', 'getDescription', 'isPublished', 'getImage', 'getContent', 'getCreationMode', 'getCreated', 'getAuthor'])
            ->getMock();
        $travel->method('getId')->willReturn(1);
        $travel->method('getTitle')->willReturn('test_travel');
        $travel->method('getDescription')->willReturn('To make sure ids work properly');
        $travel->method('isPublished')->willReturn(true);
        $travel->method('getImage')->willReturn('https://host.com/image.jpg');
        $travel->method('getContent')->willReturn(['foo' => 'bar']);
        $travel->method('getCreationMode')->willReturn('Travel test mode');
        $travel->method('getCreated')->willReturn(new DateTime('2000-01-01'));
        $travel->method('getAuthor')->willReturn($this->buildUser());
        return $travel;
    }
}
