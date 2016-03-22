<?php
namespace Test;

use Api\Test\FunctionalTestCase;

class TravelFavoriteTest extends FunctionalTestCase
{
    public function testAddGetRemoveTravelFavorite()
    {
        $this->createAndLoginUser();
        $id = $this->apiClient->createTravel('Hobbit', 'There and back again');
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
