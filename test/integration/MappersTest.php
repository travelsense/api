<?php
/**
 * MappersTest.php
 * Date: 5/16/16
 * Time: 5:32 PM
 */

namespace Api;

use Api\Mapper\DB\BookingMapper;
use Api\Mapper\DB\CategoryMapper;
use Api\Mapper\DB\CommentMapper;
use Api\Mapper\DB\FlaggedCommentMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Mapper\DB\UserMapper;
use Api\Model\Travel\Category;
use Api\Model\Travel\Comment;
use Api\Model\Travel\Travel;
use Api\Model\User;
use Api\Test\DatabaseTrait;
use PDO;

class MappersTest extends \PHPUnit_Framework_TestCase
{
    use DatabaseTrait;

    /**
     * @var UserMapper
     */
    private $user_mapper;

    /**
     * @var TravelMapper
     */
    private $travel_mapper;

    /**
     * @var CategoryMapper
     */
    private $category_mapper;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var FlaggedCommentMapper
     */
    private $flagged_comment_mapper;

    /**
     * @var CommentMapper
     */
    private $comment_mapper;

    /**
     * @var BookingMapper
     */
    private $booking_mapper;

    public function setUp()
    {
        $app = Application::createByEnvironment('test');
        self::resetDatabase($app);

        $this->user_mapper = $app['mapper.db.user'];
        $this->travel_mapper = $app['mapper.db.travel'];
        $this->category_mapper = $app['mapper.db.category'];
        $this->pdo = $app['db.main.pdo'];
        $this->flagged_comment_mapper = $app['mapper.db.flagged_comment'];
        $this->comment_mapper = $app['mapper.db.comment'];
        $this->booking_mapper = $app['mapper.db.booking'];
    }

    /**
     * UserMapper
     */
    public function testUserMapper()
    {
        $mapper = $this->user_mapper;
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
        $this->category_mapper->insert($category);
        $this->assertTrue(is_int($category->getId()));
        $this->assertSameCategories($category, $this->category_mapper->fetchById($category->getId()));
    }

    /**
     * TravelMapper
     */
    public function testTravelMapperFavorites()
    {
        $user_a = $this->createUser('a');
        $this->user_mapper->insert($user_a);
        $user_b = $this->createUser('b');
        $this->user_mapper->insert($user_b);

        // User A created Travel
        $travel = $this->createTravel($user_a, 'a');
        $this->travel_mapper->insert($travel);

        // User B favorited Travel
        $this->travel_mapper->addFavorite($travel->getId(), $user_b->getId());

        // User B gets their favorites
        $favorites = $this->travel_mapper->fetchFavorites($user_b->getId());
        $this->assertCount(1, $favorites);
        // The author is User A
        $this->assertSameUsers($user_a, $favorites[0]->getAuthor());
        // And it's the same travel
        $this->assertSameTravels($travel, $favorites[0]);
    }

    /**
     *
     */
    public function testTravelCategories()
    {
        $user = $this->createUser('a');
        $this->user_mapper->insert($user);

        $cat_a = $this->createCategory('a');
        $this->category_mapper->insert($cat_a);

        $cat_b = $this->createCategory('b');
        $this->category_mapper->insert($cat_b);

        $travel_a = $this->createTravel($user, 'a');
        $travel_a->setCategoryIds([$cat_a->getId()]);
        $this->travel_mapper->insert($travel_a);
        $travel_b = $this->createTravel($user, 'b');

        $this->travel_mapper->insert($travel_b);

        $cat_list = $this->category_mapper->fetchByTravelId($travel_a->getId());
        $this->assertSameCategories($cat_a, $cat_list[0]);

        $this->assertEquals([], $this->travel_mapper->fetchByCategory($cat_b->getName(), 1, 0));
        $travel_list = $this->travel_mapper->fetchByCategory($cat_a->getName(), 1, 0);
        $this->assertSameTravels($travel_a, $travel_list[0]);

        $this->assertEquals(
            [$cat_a->getId()],
            $this->travel_mapper
                ->fetchById($travel_a->getId())
                ->getCategoryIds()
        );
    }

    public function testFetchAllCategories()
    {
        $cat_a = $this->createCategory('a');
        $this->category_mapper->insert($cat_a);
        $cat_b = $this->createCategory('b');
        $this->category_mapper->insert($cat_b);

        $categories = $this->category_mapper->fetchAll();
        $cat_ids = [];
        $cat_names = [];
        foreach ($categories as $category) {
            $cat_ids [] = $category->getId();
            $cat_names [] = $category->getName();
        }
        $this->assertCount(2, $categories);
        $this->assertEquals([$cat_a->getId(), $cat_b->getId()], $cat_ids);
        $this->assertEquals(['a', 'b'], $cat_names);
    }

    public function testFetchAllCategoriesByName()
    {
        $cat_ab = $this->createCategory('ab');
        $this->category_mapper->insert($cat_ab);
        $cat_abc = $this->createCategory('abc');
        $this->category_mapper->insert($cat_abc);
        $cat_abcd = $this->createCategory('abcd');
        $this->category_mapper->insert($cat_abcd);
        $cat_b = $this->createCategory('b');
        $this->category_mapper->insert($cat_b);

        $categories = $this->category_mapper->fetchAllByName('a');
        $cat_ids = [];
        $cat_names = [];
        foreach ($categories as $category) {
            $cat_ids [] = $category->getId();
            $cat_names [] = $category->getName();
        }
        $this->assertCount(3, $categories);
        $this->assertEquals([$cat_ab->getId(), $cat_abc->getId(), $cat_abcd->getId()], $cat_ids);
        $this->assertEquals(['ab', 'abc', 'abcd'], $cat_names);
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
        $this->assertEquals($a->getCategoryIds(), $b->getCategoryIds());
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

    /**
     * @param int $author_id
     * @param int $travel_id
     * @param string $text
     * @return Comment
     */
    public function createComment(int $author_id, int $travel_id, string $text): Comment
    {
        $comment = new Comment();
        $comment
            ->setAuthorId($author_id)
            ->setTravelId($travel_id)
            ->setText($text)
        ;
        return $comment;
    }
    
    public function testFlagComment()
    {
        $user = $this->createUser('testUser');
        $this->user_mapper->insert($user);

        $travel = $this->createTravel($user, 'testTravel');
        $this->travel_mapper->insert($travel);

        $comment = $this->createComment($user->getId(), $travel->getId(), 'flagged this comment');
        $this->comment_mapper->insert($comment);

        $mapper = $this->flagged_comment_mapper;
        
        $mapper->flagComment($user->getId(), $comment->getId());
        $select = $this->pdo->prepare('SELECT comment_id, user_id FROM flagged_comments');
        $select->execute();
        $row = $select->fetch(PDO::FETCH_NAMED);
        $this->assertEquals(
            ['comment_id' => $comment->getId(), 'user_id' => $user->getId()],
            $row
        );
    }
    
    public function testBookingMapper()
    {
        $author_a = $this->createUser('a');
        $this->user_mapper->insert($author_a);

        $travel_a1 = $this->createTravel($author_a, 'a1');
        $this->travel_mapper->insert($travel_a1);
        $travel_a2 = $this->createTravel($author_a, 'a2');
        $this->travel_mapper->insert($travel_a2);

        $author_b = $this->createUser('author2');
        $this->user_mapper->insert($author_b);

        $travel_b1 = $this->createTravel($author_b, 'b1');
        $this->travel_mapper->insert($travel_b1);

        $booker = $this->createUser('booker');
        $this->user_mapper->insert($booker);

        // $travel_a1 presents twice to test double booking
        foreach ([$travel_a1, $travel_a1, $travel_a2, $travel_b1] as $travel) {
            $this->booking_mapper->registerBooking($booker->getId(), $travel->getId());
        }

        // getBookingsTotal
        $this->assertEquals(2, $this->booking_mapper->getBookingsTotal($author_a->getId()));
        $this->assertEquals(1, $this->booking_mapper->getBookingsTotal($author_b->getId()));

        // getStats
        $stats = $this->booking_mapper->getStats($author_a->getId());
        for ($i = 0; $i < count($stats); $i++) {
            $item = $stats[$i];
            $this->assertRegExp('/^\d{4}-\d{2}-\d{2}$/', $item['date']);
            if ($i === 6) {
                $this->assertEquals(2, $item['count']);
            } else {
                $this->assertEquals(0, $item['count']);
            }
        }
    }

    public function testMarkDeleted()
    {
        $user = $this->createUser('testUser');
        $this->user_mapper->insert($user);

        $travel = $this->createTravel($user, 'testTravel');
        $this->travel_mapper->insert($travel);

        $mapper = $this->travel_mapper;

        $mapper->markDeleted($travel->getId(), $deleted = true);
        $select = $this->pdo->prepare('SELECT id, deleted FROM travels WHERE deleted=true');
        $select->execute();
        $row = $select->fetch(PDO::FETCH_NAMED);
        $this->assertEquals(
            ['id' => $travel->getId(), 'deleted' => true],
            $row
        );
    }
    
    public function testFetchById()
    {
        $user = $this->createUser('testUser');
        $this->user_mapper->insert($user);

        $travel = $this->createTravel($user, 'testTravel');
        $this->travel_mapper->insert($travel);

        $travel_test = $this->travel_mapper->fetchById($travel->getId());

        $this->assertSameTravels($travel_test, $travel);
    }
    
    public function testFetchByAuthorId()
    {
        $user = $this->createUser('testUser');
        $this->user_mapper->insert($user);

        $travel = $this->createTravel($user, 'testTravel');
        $this->travel_mapper->insert($travel);

        $travel_list = $this->travel_mapper->fetchByAuthorId($user->getId(), 1, 0);

        $this->assertSameTravels($travel_list[0], $travel);
    }

    public function testFetchPublishedByCategory()
    {
        $user = $this->createUser('testUser');
        $this->user_mapper->insert($user);

        $cat = $this->createCategory('testCategory');
        $this->category_mapper->insert($cat);

        $travel = $this->createTravel($user, 'testTravel');
        $travel->setCategoryIds([$cat->getId()]);
        $this->travel_mapper->insert($travel);

        $travel_list = $this->travel_mapper->fetchByCategory($cat->getName(), 1, 0);
        
        $this->assertSameTravels($travel_list[0], $travel);
    }
}
