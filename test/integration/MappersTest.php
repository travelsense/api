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
use Api\Mapper\DB\User\RoleMapper;
use Api\Mapper\DB\UserMapper;
use Api\Model\Travel\Category;
use Api\Model\Travel\Comment;
use Api\Model\Travel\Travel;
use Api\Model\User;
use Api\Test\DatabaseTrait;
use Doctrine\DBAL\Connection;
use PDO;
use PHPUnit\Framework\TestCase;

class MappersTest extends TestCase
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
     * @var Connection
     */
    private $connection;

    /**
     * @var CommentMapper
     */
    private $comment_mapper;

    /**
     * @var BookingMapper
     */
    private $booking_mapper;

    /**
     * @var RoleMapper
     */
    private $user_role_mapper;

    public static function setUpBeforeClass()
    {
        try {
            self::resetDatabase(new Application('test'));
        } catch (\PDOException $e) {
            if (false !== strpos($e->getMessage(), 'SQLSTATE[08006]')) {
                self::markTestSkipped('DB is down');
            } else {
                throw $e;
            }
        }
    }

    public function setUp()
    {
        $app = new Application('test');

        $this->connection = $app['dbs']['main'];

        $this->user_mapper = $app['mapper.db.user'];
        $this->travel_mapper = $app['mapper.db.travel'];
        $this->category_mapper = $app['mapper.db.category'];
        $this->comment_mapper = $app['mapper.db.comment'];
        $this->booking_mapper = $app['mapper.db.booking'];
        $this->user_role_mapper = $app['mapper.db.user_role'];
    }

    public function tearDown()
    {
        $this->connection->exec('DELETE FROM users CASCADE');
        $this->connection->exec('DELETE FROM travel_categories');
        $this->connection->exec('DELETE FROM categories CASCADE');
    }

    /**
     * UserMapper
     */

    public function testUserMapper()
    {
        $mapper = $this->user_mapper;
        $user = $this->createUser('a');

        // Test on empty db
        $non_existent_email = 'x' . $user->getEmail();
        $this->assertFalse($mapper->emailExists($non_existent_email));
        $this->assertNull($mapper->fetchByEmail($non_existent_email));
        $this->assertNull($mapper->fetchById($user->getId() + 1));
        $this->assertNull($mapper->fetchByEmailAndPassword($non_existent_email, $user->getPassword()));

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

         // Update picture
        $mapper->updatePic($user->getId(), 'https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5');
        $this->assertSameUsers($user, $mapper->fetchByEmail($user->getEmail()));
    }

    /**
     * CategoryMapper
     */

    public function testCategoryMapper()
    {
        $category = $this->createCategory('a');
        $this->assertTrue(is_int($category->getId()));
        $this->assertSameCategories($category, $this->category_mapper->fetchById($category->getId()));

        $this->assertInternalType('array', $this->category_mapper->fetchFeaturedCategoryNames());
    }

    public function testCategoryMapperFetchAllCategories()
    {
        $cat_a = $this->createCategory('a');
        $cat_b = $this->createCategory('b');

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

    public function testCategoryMapperFetchAllCategoriesByName()
    {
        $cat_ab = $this->createCategory('ab');
        $cat_abc = $this->createCategory('abc');
        $cat_abcd = $this->createCategory('abcd');
        $this->createCategory('b');

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

    /**
     * TravelMapper
     */

    public function testTravelMapperFavorites()
    {
        $user_a = $this->createUser('a');
        $user_b = $this->createUser('b');

        // User A created Travel
        $travel = $this->createTravel($user_a, 'a');

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


    public function testTravelMapperTravelCategories()
    {
        $user = $this->createUser('a');
        $cat_a = $this->createCategory('a');
        $cat_b = $this->createCategory('b');
        $travel_a = $this->createTravel($user, 'a', [$cat_a->getId()]);
        $this->createTravel($user, 'b');

        $cat_list = $this->category_mapper->fetchByTravelId($travel_a->getId());
        $cat_ids_list = $this->category_mapper->fetchIdsByTravelId($travel_a->getId());
        $this->assertSameCategories($cat_a, $cat_list[0]);
        $this->assertEquals([$cat_a->getId()], $cat_ids_list);

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

    public function testTravelMapperMarkDeleted()
    {
        $user = $this->createUser('testUser');
        $travel = $this->createTravel($user, 'testTravel');
        $this->travel_mapper->markDeleted($travel->getId(), $deleted = true);
        $select = $this->connection->prepare('SELECT id, deleted FROM travels WHERE deleted=true');
        $select->execute();
        $row = $select->fetch(PDO::FETCH_NAMED);
        $this->assertEquals(
            ['id' => $travel->getId(), 'deleted' => true],
            $row
        );
    }

    public function testTravelMapperFetchById()
    {
        $user = $this->createUser('testUser');
        $travel = $this->createTravel($user, 'testTravel');
        $travel_test = $this->travel_mapper->fetchById($travel->getId());
        $this->assertSameTravels($travel_test, $travel);
    }

    public function testTravelMapperFetchByAuthorId()
    {
        $user = $this->createUser('testUser');
        $travel = $this->createTravel($user, 'testTravel');
        $travel_list = $this->travel_mapper->fetchByAuthorId($user->getId(), 1, 0);
        $this->assertSameTravels($travel_list[0], $travel);
    }

    public function testTravelMapperFetchPublishedByCategory()
    {
        $user = $this->createUser('testUser');
        $cat = $this->createCategory('testCategory');
        $travel = $this->createTravel($user, 'testTravel', [$cat->getId()]);
        $travel_list = $this->travel_mapper->fetchByCategory($cat->getName(), 1, 0);
        $this->assertSameTravels($travel_list[0], $travel);
    }

    /**
     * Comment mapper
     */

    public function testCommentMapperFlagComment()
    {
        $user = $this->createUser('testUser');
        $travel = $this->createTravel($user, 'testTravel');
        $comment = $this->createComment($user->getId(), $travel->getId(), 'flagged this comment');
        $mapper = $this->comment_mapper;
        $mapper->flagComment($user->getId(), $comment->getId());
        $select = $this->connection->prepare('SELECT comment_id, user_id FROM flagged_comments');
        $select->execute();
        $row = $select->fetch(PDO::FETCH_NAMED);
        $this->assertEquals(
            ['comment_id' => $comment->getId(), 'user_id' => $user->getId()],
            $row
        );
    }

    /**
     * BookingMapper
     */
    
    public function testBookingMapper()
    {
        $author_a = $this->createUser('a');

        $travel_a1 = $this->createTravel($author_a, 'a1');
        $travel_a2 = $this->createTravel($author_a, 'a2');

        $author_b = $this->createUser('author2');

        $travel_b1 = $this->createTravel($author_b, 'b1');

        $booker = $this->createUser('booker');

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

    /**
     * User\RoleMapper
     */

    public function testUserRoleMapper()
    {
        $user = $this->createUser('a');
        $this->assertEquals([], $this->user_role_mapper->getRoles($user->getId()));

        $this->user_role_mapper->grantRole($user->getId(), 'moderator');
        $this->user_role_mapper->grantRole($user->getId(), 'admin');

        $this->assertEquals(
            ['admin', 'moderator'],
            $this->user_role_mapper->getRoles($user->getId())
        );

        $this->user_role_mapper->withdrawRole($user->getId(), 'admin');

        $this->assertEquals(
            ['moderator'],
            $this->user_role_mapper->getRoles($user->getId())
        );
    }

    /**
     * Helpers
     */

    /**
     * @param Category $a
     * @param Category $b
     */
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
        $this->category_mapper->insert($category);
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
        $this->assertEquals($a->getTitle(), $b->getTitle());
        $this->assertEquals($a->getCategoryIds(), $b->getCategoryIds());
    }

    /**
     * @param User   $author
     * @param string $title
     * @param int[]  $category_ids
     * @return Travel
     */
    private function createTravel(User $author, string $title, array $category_ids = []): Travel
    {
        $travel = new Travel();
        $travel
            ->setAuthor($author)
            ->setTitle($title)
            ->setDescription($title)
            ->setCategoryIds($category_ids)
        ;
        $this->travel_mapper->insert($travel);
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
        $this->user_mapper->insert($user);
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
    private function createComment(int $author_id, int $travel_id, string $text): Comment
    {
        $comment = new Comment();
        $comment
            ->setAuthorId($author_id)
            ->setTravelId($travel_id)
            ->setText($text)
        ;
        $this->comment_mapper->insert($comment);
        return $comment;
    }
}
