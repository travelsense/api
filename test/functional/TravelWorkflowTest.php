<?php
namespace Test;

use Api\Test\ApiClientException;
use Api\Test\FunctionalTestCase;

class TravelWorkflowTest extends FunctionalTestCase
{
    public function testTravelCreationAndRetrieval()
    {
        $this->createAndLoginUser();
        $id = $this->apiClient->createTravel([
            'title'       => 'First Travel',
            'description' => 'To make sure ids work properly',
            'image'       => 'https://host.com/image.jpg',
            'content'     => ['foo' => 'bar'],
        ]);
        $this->assertEquals(1, $id);
        $id = $this->apiClient->createTravel([
            'title'       => 'Hobbit',
            'description' => 'There and back again',
            'image'       => 'https://host.com/image.jpg',
            'content'     => ['foo' => 'bar'],
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

    private function checkGetTravel(int $id)
    {
        $travel = $this->apiClient->getTravel($id);
        $author = $travel->author;
        $this->assertEquals('Hobbit', $travel->title);
        $this->assertEquals('There and back again', $travel->description);
        $this->assertEquals('https://host.com/image.jpg', $travel->image);

        $this->assertEquals('Pushkin', $author->lastName, 'Wrong author');
        $this->assertEquals((object)['foo' => 'bar'], $travel->content);

        foreach (['firstName', 'lastName', 'id', 'picture'] as $attr) {
            $this->assertObjectHasAttribute($attr, $author);
        }
    }

    private function checkUpdateTravel(int $id)
    {
        $this->apiClient->updateTravel($id, [
            'title'       => 'Two Towers',
            'description' => 'Before the Return of the King',
            'image'       => 'https://host.com/new_image.jpg',
            'content'     => ['pew' => 'boom'],
        ]);
        $travelUpdated = $this->apiClient->getTravel($id);
        $this->assertEquals('Two Towers', $travelUpdated->title);
        $this->assertEquals('Before the Return of the King', $travelUpdated->description);
        $this->assertEquals('https://host.com/new_image.jpg', $travelUpdated->image);
        $this->assertEquals((object)['pew' => 'boom'], $travelUpdated->content);
    }

    private function checkDeleteTravel(int $id)
    {
        $this->apiClient->deleteTravel($id);
        try {
            $this->apiClient->getTravel($id);
            $this->fail("travel record still exists after deleteTravel()");
        } catch (ApiClientException $e) {
            if ($e->getCode() !== 404) {
                $this->fail("Wrong error code for getting deleted travel: " . $e->getMessage());
            }
        }
    }

    private function checkAddRemoveFavorites(int $id)
    {
        $this->apiClient->addTravelToFavorites($id);
        $favoriteTravels = $this->apiClient->getFavoriteTravels();
        $this->assertEquals(1, count($favoriteTravels));
        $travel = $favoriteTravels[0];
        $this->assertEquals('Hobbit', $travel->title);
        $this->assertEquals('There and back again', $travel->description);
        $this->apiClient->removeTravelFromFavorites($id);
        $favoriteTravels = $this->apiClient->getFavoriteTravels();
        $this->assertEmpty($favoriteTravels);
    }
    
    private function checkGetMyTravels()
    {
        $travels = $this->apiClient->getMyTravels();
        $this->assertEquals(2, count($travels));
    }
    
    private function checkAddRemoveComments($id)
    {
        $ids = [];
        for ($i = 0; $i < 20; $i++) {
            $ids[] = $this->apiClient->addTravelComment($id, "Comment $i");
        }

        $limit = 3;
        $offset = 5;
        $comments = $this->apiClient->getTravelComments($id, $limit, $offset);

        $this->assertEquals($limit, count($comments));

        // 19 (latest) - 5 (offset) = 14
        $this->assertEquals('Comment 14', $comments[0]->text);
        $this->assertEquals('Comment 13', $comments[1]->text);
        $this->assertEquals('Comment 12', $comments[2]->text);

        // Remove the last comment
        $killMe = end($ids);
        $this->apiClient->deleteTravelComment($killMe);

        $comments = $this->apiClient->getTravelComments($id, $limit, $offset);

        // 18 (latest) - 5 (offset) = 13
        $this->assertEquals('Comment 13', $comments[0]->text);
        $this->assertEquals('Comment 12', $comments[1]->text);
        $this->assertEquals('Comment 11', $comments[2]->text);
    }
}
