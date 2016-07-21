<?php
namespace Test;

use Api\Test\ApiClientException;
use Api\Test\FunctionalTestCase;

class FunctionalWebTest extends FunctionalTestCase
{
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
            'content'     => ['foo' => 'bar'],
            'creation_mode' => 'First Travel test mode',
            'category_ids' => [1, 2],
        ]);
        $this->assertEquals(1, $id);
        $id = $this->client->createTravel([
            'title'       => 'Hobbit',
            'description' => 'There and back again',
            'image'       => 'https://host.com/image.jpg',
            'content'     => ['foo' => 'bar'],
            'creation_mode' => 'Hobbit test mode',
            'category_ids' => [1, 2],
        ]);
        $this->assertEquals(2, $id);

        $this->checkGetTravel(2);
        $this->checkAddRemoveFavorites(2);
        $this->checkUpdateTravel(2);
        $this->checkAddRemoveComments(1);
        $this->checkGetMyTravels();

        $this->checkDeleteTravel(1);
        $this->checkDeleteTravel(2);
    }

    public function testBookingStats()
    {
        $this->createAndLoginUser();
        $this->client->createCategory('test cat1');
        $this->client->createCategory('test cat2');
        $id = $this->client->createTravel([
            'title'       => 'First Travel',
            'description' => 'To make sure ids work properly',
            'image'       => 'https://host.com/image.jpg',
            'content'     => ['foo' => 'bar'],
            'creation_mode' => 'First Travel test mode',
            'category_ids' => [1, 2],
        ]);

        $this->client->registerBooking($id);
        $stats = $this->client->getStats();
        $this->assertEquals(1, $stats->bookingsTotal);
        $this->assertEquals(0.1, $stats->rewardTotal);
        $total = 0;
        foreach ($stats->bookingsLastWeek as $item) {
            $this->assertRegExp('/^\d{4}-\d{2}-\d{2}$/', $item->date);
            $total += $item->count;
        }
        $this->assertEquals(1, $total);
    }


    private function checkGetTravel(int $id)
    {
        $travel = $this->client->getTravel($id);
        $author = $travel->author;
        $this->assertEquals('Hobbit', $travel->title);
        $this->assertEquals('There and back again', $travel->description);
        $this->assertEquals('https://host.com/image.jpg', $travel->image);
        $this->assertEquals(false, $travel->published);
        $this->assertEquals('Hobbit test mode', $travel->creation_mode);

        $this->assertEquals('Pushkin', $author->lastName, 'Wrong author');
        $this->assertEquals((object)['foo' => 'bar'], $travel->content);
        $this->assertEquals([1, 2], $travel->category_ids);

        foreach (['firstName', 'lastName', 'id', 'picture'] as $attr) {
            $this->assertObjectHasAttribute($attr, $author);
        }
    }

    private function checkUpdateTravel(int $id)
    {
        $this->client->updateTravel($id, [
            'title'       => 'Two Towers',
            'description' => 'Before the Return of the King',
            'image'       => 'https://host.com/new_image.jpg',
            'published'   => true,
            'content'     => ['pew' => 'boom'],
            'creation_mode' => 'Two Towers test mode',
            'category_ids' => [1, 3],
        ]);
        $travel = $this->client->getTravel($id);
        $this->assertEquals('Two Towers', $travel->title);
        $this->assertEquals('Before the Return of the King', $travel->description);
        $this->assertEquals('https://host.com/new_image.jpg', $travel->image);
        $this->assertEquals(true, $travel->published);
        $this->assertEquals((object)['pew' => 'boom'], $travel->content);
        $this->assertEquals('Two Towers test mode', $travel->creation_mode);
        $this->assertEquals([1, 3], $travel->category_ids);
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
