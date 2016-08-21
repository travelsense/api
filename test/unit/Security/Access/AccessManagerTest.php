<?php
namespace Api\Security\Access;

use Api\Mapper\DB\User\RoleMapper;
use Api\Model\Travel\Travel;
use Api\Model\User;
use PHPUnit_Framework_MockObject_MockObject;

class AccessManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessManager
     */
    private $manager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $roleMapper;

    public function setUp()
    {
        $this->roleMapper = $this->getMockBuilder(RoleMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRoles'])
            ->getMock();
        $this->manager = new AccessManager($this->roleMapper);
    }

    public function testReadGrantedAlways()
    {
        $actor = $this->getMockForAbstractClass(ActorInterface::class);
        $subject = $this->getMockForAbstractClass(SubjectInterface::class);
        $this->assertTrue($this->manager->isGranted($actor, Action::READ, $subject));
    }

    public function testWriteGranted()
    {
        $author = new User();
        $author->setId(42);

        $other = new User();
        $other->setId(1);

        $moderator = new User();
        $moderator->setId(2);

        $travel = new Travel();
        $travel->setAuthorId(42);

        $this->roleMapper->method('getRoles')
            ->willReturnMap([
                [1, []],
                [2, [Role::MODERATOR]],
            ]);

        $this->assertTrue($this->manager->isGranted($author, Action::WRITE, $travel));
        $this->assertTrue($this->manager->isGranted($moderator, Action::WRITE, $travel));
        $this->assertFalse($this->manager->isGranted($other, Action::WRITE, $travel));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown action: foo
     */
    public function testUnknownAction()
    {
        $actor = $this->getMockForAbstractClass(ActorInterface::class);
        $subject = $this->getMockForAbstractClass(SubjectInterface::class);
        $this->assertTrue($this->manager->isGranted($actor, 'foo', $subject));
    }
}
