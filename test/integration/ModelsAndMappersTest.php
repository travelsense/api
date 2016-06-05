<?php
/**
 * MappersTest.php
 * Date: 5/16/16
 * Time: 5:32 PM
 */

namespace Api;

use Api\Mapper\DB\CategoryMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Mapper\DB\UserMapper;
use Api\Mapper\DB\FlaggedCommentMapper;
use Api\Mapper\DB\CommentMapper;
use Api\Model\Travel\Category;
use Api\Model\Travel\Travel;
use Api\Model\User;
use Api\Model\Travel\Comment;
use Api\Test\DatabaseTrait;
use PDO;

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

    /**
     * @var CategoryMapper
     */
    private $categoryMapper;

    /*
     * @var PDO
     */
    private $pdo;

    /*
     *@var FlaggedCommentMapper
     */
    private $flaggedCommentMapper;

    /*
     * @var CommentMapper
     */
    private $commentMapper;
    
    public function setUp()
    {
        $app = Application::createByEnvironment('test');
        self::resetDatabase($app);

        $this->userMapper = $app['mapper.db.user'];
        $this->travelMapper = $app['mapper.db.travel'];
        $this->categoryMapper = $app['mapper.db.category'];
        $this->pdo = $app['db.main.pdo'];
        $this->flaggedCommentMapper = $app['mapper.db.flagged_comments'];
        $this->commentMapper = $app['mapper.db.travel_comments'];
    }

    /**
     * UserMapper
     */
    public function testUserMapper()
    {
        $mapper = $this->userMapper;
        $user = $this->createUser('a');
        $this->assertNull($user->getId());

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

    /**
     * CategoryMapper
     */
    public function testCategoryMapper()
    {
        $category = $this->createCategory('a');
        $this->assertNull($category->getId());
        $this->categoryMapper->insert($category);
        $this->assertTrue(is_int($category->getId()));
        $this->assertSameCategories($category, $this->categoryMapper->fetchBylId($category->getId()));
    }

    /**
     * TravelMapper
     */
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
     *
     */
    public function testTravelCategories()
    {
        $userA = $this->createUser('a');
        $this->userMapper->insert($userA);

        $catA = $this->createCategory('a');
        $this->categoryMapper->insert($catA);

        $catB = $this->createCategory('b');
        $this->categoryMapper->insert($catB);

        $travelA = $this->createTravel($userA, 'a');
        $travelA->setCategoryId($catA->getId());
        $this->travelMapper->insert($travelA);

        $travelB = $this->createTravel($userA, 'b');
        $this->travelMapper->insert($travelB);

        $catList = $this->categoryMapper->fetchByTravelId($travelA->getId());
        $this->assertSameCategories($catA, $catList[0]);

        $this->assertEquals([], $this->travelMapper->fetchByCategory($catB->getName(), 1, 0));
        $travelList = $this->travelMapper->fetchByCategory($catA->getName(), 1, 0);
        $this->assertSameTravels($travelA, $travelList[0]);

        $this->assertEquals(
            $catA->getId(),
            $this->travelMapper
                ->fetchById($travelA->getId())
                ->getCategoryId()
        );
    }


    private function assertSameCategories(Category $a, Category $b)
    {
        $this->assertTrue(
            $a->getId() === $b->getId()
            && $a->getName() === $b->getName()
        );
    }

    /**
     * @param string $token
     * @return Category
     */
    private function createCategory(string $token): Category
    {
        $category = new Category();
        $category
            ->setName($token)
        ;
        return $category;
    }

    /**
     * @param Travel $a
     * @param Travel $b
     */
    private function assertSameTravels(Travel $a, Travel $b)
    {
        $this->assertEquals($a->getId(), $b->getId());
        $this->assertEquals($a->getAuthorId(), $b->getAuthorId());
        $this->assertEquals($a->getContent(), $b->getContent());
        $this->assertEquals($a->getTitle(), $b->getTitle());
        $this->assertEquals($a->getCategoryId(), $b->getCategoryId());
    }

    /**
     * @param User $author
     * @param string $token
     * @return Travel
     */
    private function createTravel(User $author, string $token): Travel
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

    public function createComment(int $travelId, string $text): Comment
    {
        $comment = new Comment();
        $comment
            ->setTravelId($travelId)
            ->setText($text)
        ;
        return $comment;
    }
    
    public function testFlagComment()
    {
        $user = $this->createUser('a');
        $travel = $this->createTravel($user, 'a');
        $comment = $this->createComment($travel->getId(), 'flagged this comment');
            
        $mapper = $this->flaggedCommentMapper;
        
        $mapper->flagComment($user->getId(), $comment->getId());
        $select = $this->pdo->prepare('SELECT * FROM flagged_comments');
        $row = $select->fetch(PDO::FETCH_NAMED);
        $this->assertEquals($row, [$user->getId() => $comment->getId()]);
    }
}
