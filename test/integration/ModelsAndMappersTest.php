<?php
/**
 * MappersTest.php
 * Date: 5/16/16
 * Time: 5:32 PM
 */

namespace Api;

use Api\Mapper\DB\TravelMapper;
use Api\Mapper\DB\UserMapper;
use Api\Model\Travel\Travel;
use Api\Model\User;
use Api\Test\DatabaseTrait;

class ModelsAndMappersTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseTrait;

    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var TravelMapper
     */
    private $travelMapper;

    public function setUp()
    {
        $app = Application::createByEnvironment('test');
        self::resetDatabase($app);

        $this->userMapper = $app['mapper.db.user'];
        $this->travelMapper = $app['mapper.db.travel'];
    }

    /**
     * UserMapper
     */
    public function testUserMapper()
    {
        $mapper = $this->userMapper;
        $user = $this->createUser('a');

        // Test on empty db
        $this->assertFalse($mapper->emailExists($user->getEmail()));
        $this->assertNull($mapper->fetchByEmail($user->getEmail()));
        $this->assertNull($mapper->fetchById(1));
        $this->assertNull($mapper->fetchByEmailAndPassword($user->getEmail(), $user->getPassword()));

        // Create user, assert id changed
        $mapper->insert($user);
        $this->assertTrue(is_int($user->getId()));

        // Non empty db
        $this->assertTrue($mapper->emailExists($user->getEmail()));
        $this->assertSameUsers($user, $mapper->fetchByEmail($user->getEmail()));
        $this->assertSameUsers($user, $mapper->fetchByEmailAndPassword($user->getEmail(), $user->getPassword()));

        $mapper->confirmEmail($user->getEmail());
        $this->assertTrue($mapper->fetchById($user->getId())->isEmailConfirmed());

        // Update
        $user
            ->setEmail('new_email@example.com')
            ->setFirstName('New')
            ->setLastName("New Tester");

        $mapper->update($user);
        $this->assertSameUsers($user, $mapper->fetchByEmail($user->getEmail()));
    }

    public function testTravelMapperFavorites()
    {
        $userA = $this->createUser('a');
        $this->userMapper->insert($userA);
        $userB = $this->createUser('b');
        $this->userMapper->insert($userB);
        
        // UserA created TravelA
        $travelA = $this->createTravel($userA, 'a');
        $this->travelMapper->insert($travelA);
        
        // UserB favorited TravelA
        $this->travelMapper->addFavorite($travelA->getId(), $userB->getId());
        
        // UserB gets their favorites
        $favorites = $this->travelMapper->fetchFavorites($userB->getId());
        $this->assertCount(1, $favorites);
        // The author is UserA
        $this->assertSameUsers($userA, $favorites[0]->getAuthor());
        // And it's the same travel
        $this->assertSameTravels($travelA, $favorites[0]);
    }


    /**
     * @param Travel $a
     * @param Travel $b
     */
    private function assertSameTravels(Travel $a, Travel $b)
    {
        $this->assertTrue(
            $a->getId() === $b->getId()
            && $a->getAuthorId() === $b->getAuthorId()
            && $a->getContent() === $b->getContent()
            && $a->getDescription() === $b->getDescription()
            && $a->getTitle() === $b->getTitle()
        );
    }

    /**
     * @param User $author
     * @param $token
     * @return Travel
     */
    private function createTravel(User $author, $token): Travel
    {
        $travel = new Travel();
        $travel
            ->setAuthor($author)
            ->setContent($token)
            ->setTitle($token)
            ->setDescription($token);
        return $travel;
    }
    
    /**
     * @param string $token
     * @return User
     */
    private function createUser(string $token): User
    {
        $user = new User();
        $user
            ->setEmail("$token@example.com")
            ->setFirstName(ucfirst($token))
            ->setLastName("Tester")
            ->setPassword($token)
        ;
        return $user;
    }

    /**
     * @param User $a
     * @param User $b
     */
    private function assertSameUsers(User $a, User $b)
    {
        $this->assertTrue(
            $a->getId() === $b->getId()
            && $a->getEmail() === $b->getEmail()
            && $a->getFirstName() === $b->getFirstName()
            && $a->getLastName() === $b->getLastName()
            && $a->isEmailConfirmed() === $b->isEmailConfirmed()
        );
    }
}