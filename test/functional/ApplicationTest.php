<?php
namespace Test;

use Api\Application;
use Api\Mapper\DB\CategoryMapper;
use Api\Model\Travel\Category;
use Api\Service\Mailer;
use Api\Test\ApplicationTestCase;
use Api\Test\DatabaseTrait;
use Api\Test\FunctionalTestCase;
use Doctrine\DBAL\Connection;
use HopTrip\ApiClient\ApiClient;
use HopTrip\ApiClient\ApiClientException;

class ApplicationTest extends ApplicationTestCase
{
    use DatabaseTrait;

    /**
     * @var CategoryMapper
     */
    private $category_mapper;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $airportAction = [
        "offsetStart" => 0,
        "hotels" => [],
        "id" => 2,
        "airports" => [],
        "offsetEnd" => 0,
        "type" => "flight",
        "sightseeings" => [],
        "car" => false,
        "index" => -1,
        "end_index" => -1,
        "transportation" => 1,
    ];


    /**
     * @var ApiClient
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();
        $this->resetDatabase($this->app);
        $this->client = $this->createApiClient();
    }

    public function createApplication()
    {
        return new Application('test');
    }

    public function testUpdateUserDetails()
    {
        $this->createAndLoginUser();
        $user = $this->client->getCurrentUser();

        $this->assertEquals(1, $user->id);
        $this->assertEquals('Alexander', $user->firstName);
        $this->assertEquals('Pushkin', $user->lastName);
        $this->assertEquals('sasha@pushkin.ru', $user->email);
        $this->assertEquals('http://pushkin.ru/sasha.jpg', $user->picture);

        $this->client->updateUser([
            'id'        => 1,
            'firstName' => 'Natalia',
            'lastName'  => 'Pushkina',
            'picture'   => 'http://pushkin.ru/sasha.jpg',
            'email'     => 'sasha@pushkin.ru',
            'creator'   => false,
        ]);
        $user = $this->client->getCurrentUser();

        $this->assertEquals('Natalia', $user->firstName);
        $this->assertEquals('Pushkina', $user->lastName);
        $this->assertEquals('sasha@pushkin.ru', $user->email);
        $this->assertEquals('http://pushkin.ru/sasha.jpg', $user->picture);
    }
    
    public function testTravelCreationAndRetrieval()
    {
        $this->createAndLoginUser();
        $this->client->createCategory('test cat1');
        $this->client->createCategory('test cat2');
        $this->client->createCategory('test cat3');
        $id = $this->client->createTravel([
            'title'       => 'First Travel',
            'description' => 'To make sure ids work properly',
            'image'       => 'https://host.com/image.jpg',
            'content'     => [$this->airportAction],
            'creation_mode' => 'First Travel test mode',
            'category_ids' => [1, 2],
            'transportation' => 5,
        ]);
        $this->assertEquals(1, $id);
        $id = $this->client->createTravel([
            'title'       => 'Hobbit',
            'description' => 'There and back again',
            'image'       => 'https://host.com/image.jpg',
            'content'     => [$this->airportAction],
            'creation_mode' => 'Hobbit test mode',
            'category_ids' => [1, 2],
            'transportation' => 5,
            'app_version' => 'v42',
        ]);
        $this->assertEquals(2, $id);

        $this->checkGetTravel(2);
        $this->checkAddRemoveFavorites(2);
        $this->checkUpdateTravel(2);
        $this->checkAddRemoveComments(1);
        $this->checkGetMyTravels();

        $this->checkGetTravelWithOutAuth(1);
        $this->checkGetTravelWithOutAuth(2);

        $this->checkDeleteTravel(1);
        $this->checkDeleteTravel(2);
    }

    public function testTravelCategoryGetting()
    {
        $this->createAndLoginUser();

        $app = new Application('test');

        $this->category_mapper = $app['mapper.db.category'];
        $this->connection = $app['dbs']['main'];

        $cat_a = new Category('a');
        $this->category_mapper->insert($cat_a);
        $cat_b = new Category('b');
        $this->category_mapper->insert($cat_b);

        $cats = $this->client->getTravelCategories();
        $cat_ids = [];
        $cat_names = [];
        foreach ($cats as $category) {
            $cat_ids[] = $category->id;
            $cat_names[] = $category->title;
        }
        $this->assertCount(2, $cats);
        $this->assertEquals([$cat_a->getId(), $cat_b->getId()], $cat_ids);
        $this->assertEquals(['a', 'b'], $cat_names);
    }

    public function testBookingStats()
    {
        $mailer_service = $this->createMock(Mailer::class);
        $this->app['email.service'] = $mailer_service;
        $this->createAndLoginUser();
        $this->client->createCategory('test cat1');
        $this->client->createCategory('test cat2');
        $id = $this->client->createTravel([
            'title'       => 'First Travel',
            'description' => 'To make sure ids work properly',
            'image'       => 'https://host.com/image.jpg',
            'content'     => [$this->airportAction],
            'creation_mode' => 'First Travel test mode',
            'category_ids' => [1, 2],
            'transportation' => 2,
        ]);

        $payload = json_decode(file_get_contents(__DIR__ . '/stub/booking_request.json'), true);

        $this->client->registerBooking($id, $payload);
        $stats = $this->client->getStats();
        $this->assertEquals(1, $stats->bookingsTotal);
        $this->assertEquals(12.35, $stats->rewardTotal);
        $total = 0;
        foreach ($stats->bookingsLastWeek as $item) {
            $this->assertRegExp('/^\d{4}-\d{2}-\d{2}$/', $item->date);
            $total += $item->count;
        }
        $this->assertEquals(1, $total);
    }

    /**
     * Creates a user and logs him in
     * @param string $email
     */
    protected function createAndLoginUser($email = 'sasha@pushkin.ru')
    {
        $password = '123';
        $this->client->registerUser([
            'firstName' => 'Alexander',
            'lastName'  => 'Pushkin',
            'picture'   => 'http://pushkin.ru/sasha.jpg',
            'email'     => $email,
            'password'  => $password,
            'creator'   => true,
        ]);
        $token = $this->client->getTokenByEmail($email, $password);
        $this->client->setAuthToken($token);
    }

    private function checkGetTravelWithOutAuth(int $id)
    {
        $this->client->addTravelToFavorites($id);
        $this->client->setAuthToken(null);

        $travel = $this->client->getTravel($id);
        $this->assertEquals(false, $travel->is_favorited);

        $this->client->setAuthToken($this->client->getTokenByEmail('sasha@pushkin.ru', '123'));
    }


    private function checkGetTravel(int $id)
    {
        $this->client->addTravelToFavorites($id);

        $travel = $this->client->getTravel($id);
        $author = $travel->author;
        $this->assertEquals('Hobbit', $travel->title);
        $this->assertEquals('There and back again', $travel->description);
        $this->assertEquals('https://host.com/image.jpg', $travel->image);
        $this->assertEquals(false, $travel->published);
        $this->assertEquals('Hobbit test mode', $travel->creation_mode);

        $this->assertEquals('Pushkin', $author->lastName, 'Wrong author');
        $this->assertEquals([(object) $this->airportAction], $travel->content);
        $this->assertEquals([1, 2], $travel->category_ids);
        $this->assertEquals(5, $travel->transportation);
        $this->assertEquals('v42', $travel->app_version);

        foreach (['firstName', 'lastName', 'id', 'picture'] as $attr) {
            $this->assertObjectHasAttribute($attr, $author);
        }

        $this->assertEquals(true, $travel->is_favorited);
    }

    private function checkUpdateTravel(int $id)
    {
        $this->client->removeTravelFromFavorites($id);

        $this->client->updateTravel($id, [
            'title'       => 'Two Towers',
            'description' => 'Before the Return of the King',
            'image'       => 'https://host.com/new_image.jpg',
            'published'   => true,
            'content'     => [$this->airportAction],
            'creation_mode' => 'Two Towers test mode',
            'category_ids' => [1, 3],
            'transportation' => 3,
            'app_version' => '333',
        ]);
        $travel = $this->client->getTravel($id);
        $this->assertEquals('Two Towers', $travel->title);
        $this->assertEquals('Before the Return of the King', $travel->description);
        $this->assertEquals('https://host.com/new_image.jpg', $travel->image);
        $this->assertEquals(true, $travel->published);
        $this->assertEquals('Two Towers test mode', $travel->creation_mode);
        $this->assertEquals([1, 3], $travel->category_ids);
        $this->assertEquals(3, $travel->transportation);
        $this->assertEquals('333', $travel->app_version);

        $this->assertEquals(false, $travel->is_favorited);
    }

    private function checkDeleteTravel(int $id)
    {
        $this->client->deleteTravel($id);
        try {
            $this->client->getTravel($id);
            $this->fail("travel record still exists after deleteTravel()");
        } catch (ApiClientException $e) {
            $this->assertEquals(4000, $e->getCode());
            $this->assertEquals('Travel not found', $e->getMessage());
        }
    }

    private function checkAddRemoveFavorites(int $id)
    {
        $this->client->addTravelToFavorites($id);
        $favorites = $this->client->getFavoriteTravels();
        $this->assertEquals(1, count($favorites));
        $travel = $favorites[0];
        $this->assertEquals('Hobbit', $travel->title);
        $this->assertEquals('There and back again', $travel->description);
        $this->client->removeTravelFromFavorites($id);
        $favorites = $this->client->getFavoriteTravels();
        $this->assertEmpty($favorites);
    }
    
    private function checkGetMyTravels()
    {
        $travels = $this->client->getMyTravels();
        $this->assertEquals(2, count($travels));
    }
    
    private function checkAddRemoveComments($id)
    {
        $ids = [];
        for ($i = 0; $i < 20; $i++) {
            $ids[] = $this->client->addTravelComment($id, "Comment $i");
        }

        $limit = 3;
        $offset = 5;
        $comments = $this->client->getTravelComments($id, $limit, $offset);

        $this->assertEquals($limit, count($comments));

        // 19 (latest) - 5 (offset) = 14
        $this->assertEquals('Comment 14', $comments[0]->text);
        $this->assertEquals('Comment 13', $comments[1]->text);
        $this->assertEquals('Comment 12', $comments[2]->text);

        // Remove the last comment
        $killMe = end($ids);
        $this->client->deleteTravelComment($killMe);

        $comments = $this->client->getTravelComments($id, $limit, $offset);

        // 18 (latest) - 5 (offset) = 13
        $this->assertEquals('Comment 13', $comments[0]->text);
        $this->assertEquals('Comment 12', $comments[1]->text);
        $this->assertEquals('Comment 11', $comments[2]->text);
    }
}
