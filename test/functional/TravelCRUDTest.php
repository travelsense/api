<?php
namespace Test;

use Api\Test\ApiClientException;
use Api\Test\FunctionalTestCase;

class TravelCRUDTest extends FunctionalTestCase
{
    public function testTravelCreationAndRetrieval()
    {
        $this->createAndLoginUser();
        $id = $this->apiClient->createTravel([
            'title' => 'First Travel',
            'description' => 'To make sure ids work properly',
            'content' => ['foo' => 'bar']
        ]);
        $this->assertEquals(1, $id);
        $id = $this->apiClient->createTravel([
            'title' => 'Hobbit',
            'description' => 'There and back again',
            'content' => ['foo' => 'bar']
        ]);
        $this->assertEquals(2, $id);
        $travel = $this->apiClient->getTravel($id);
        $author = $travel->author;
        $this->assertEquals('Hobbit', $travel->title);
        $this->assertEquals('There and back again', $travel->description);

        $this->assertEquals('Pushkin', $author->lastName, 'Wrong author');
        $this->assertEquals((object) ['foo' => 'bar'], $travel->content);

        foreach (['firstName', 'lastName', 'id', 'picture'] as $attr) {
            $this->assertObjectHasAttribute($attr, $author);
        }

        $this->apiClient->updateTravel($id, [
            'title' => 'Two Towers',
            'description' => 'Before the Return of the King',
            'content' => ['pew' => 'boom']
        ]);
        $travelUpdated = $this->apiClient->getTravel($id);
        $this->assertEquals('Two Towers', $travelUpdated->title);
        $this->assertEquals('Before the Return of the King', $travelUpdated->description);
        $this->assertEquals((object) ['pew' => 'boom'], $travelUpdated->content);

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

    public function testAddGetRemoveTravelFavorite()
    {
        $this->createAndLoginUser();
        $id = $this->apiClient->createTravel([
            'title' => 'Hobbit',
            'description' => 'There and back again',
            'content' => ['foo' => 'bar']
        ]);
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
}
