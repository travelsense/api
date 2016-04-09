<?php
namespace Test;

use Api\Test\ApiClientException;
use Api\Test\FunctionalTestCase;

class TravelCRUDTest extends FunctionalTestCase
{
    public function testTravelCreationAndRetrieval()
    {
        $this->createAndLoginUser();
        $id = $this->apiClient->createTravel('First Travel', 'To make sure ids work properly', 0, ['foo' => 'bar']);
        $this->assertEquals(1, $id);
        $id = $this->apiClient->createTravel('Hobbit', 'There and back again', 0, ['foo' => 'bar']);
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

        $this->apiClient->updateTravel($id, 'Two Towers', 'Before the Return of the King', 0, ['pew' => 'boom']);
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
        $id = $this->apiClient->createTravel('Hobbit', 'There and back again', 0);
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
