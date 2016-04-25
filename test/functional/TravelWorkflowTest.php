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
            'content'     => ['foo' => 'bar'],
        ]);
        $this->assertEquals(1, $id);
        $id = $this->apiClient->createTravel([
            'title'       => 'Hobbit',
            'description' => 'There and back again',
            'content'     => ['foo' => 'bar'],
        ]);
        $this->assertEquals(2, $id);

        $this->checkTravelGet(2);
        $this->checkAddRemoveFavorites(2);
        $this->checkTravelUpdate(2);
        $this->checkAddComments(1);

        $this->checkTravelDelete(1);
        $this->checkTravelDelete(2);
    }

    private function checkTravelGet(int $id)
    {
        $travel = $this->apiClient->getTravel($id);
        $author = $travel->author;
        $this->assertEquals('Hobbit', $travel->title);
        $this->assertEquals('There and back again', $travel->description);

        $this->assertEquals('Pushkin', $author->lastName, 'Wrong author');
        $this->assertEquals((object)['foo' => 'bar'], $travel->content);

        foreach (['firstName', 'lastName', 'id', 'picture'] as $attr) {
            $this->assertObjectHasAttribute($attr, $author);
        }
    }

    private function checkTravelUpdate(int $id)
    {
        $this->apiClient->updateTravel($id, [
            'title'       => 'Two Towers',
            'description' => 'Before the Return of the King',
            'content'     => ['pew' => 'boom'],
        ]);
        $travelUpdated = $this->apiClient->getTravel($id);
        $this->assertEquals('Two Towers', $travelUpdated->title);
        $this->assertEquals('Before the Return of the King', $travelUpdated->description);
        $this->assertEquals((object)['pew' => 'boom'], $travelUpdated->content);
    }

    private function checkTravelDelete(int $id)
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
    
    private function checkAddComments($id)
    {
        $comments = [];
        for ($i = 0; $i < 20; $i++) {
            $comments[] = $this->apiClient->addTravelComment($id, "Comment $i");
        }

        $limit = 3;
        $offset = 5;
        $comments = $this->apiClient->getTravelComments($id, $limit, $offset);

        $this->assertEquals($limit, count($comments));

        // 19 (latest) - 5 (offset) = 14
        $this->assertEquals('Comment 14', $comments[0]->text);
        $this->assertEquals('Comment 13', $comments[1]->text);
        $this->assertEquals('Comment 12', $comments[2]->text);

    }
}
