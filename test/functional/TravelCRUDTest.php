<?php
namespace Test;

use Api\Test\ApiClientException;
use Api\Test\FunctionalTestCase;

class TravelCRUDTest extends FunctionalTestCase
{
    public function testTravelCreationAndRetrieval()
    {
        $this->createAndLoginUser();
        $id = $this->apiClient->createTravel('Hobbit', 'There and back again', ['foo' => 'bar']);
        $this->assertGreaterThan(0, $id);
        $travel = $this->apiClient->getTravel($id);
        $author = $travel->author;
        $this->assertEquals('Hobbit', $travel->title);
        $this->assertEquals('There and back again', $travel->description);

        $this->assertEquals('Pushkin', $author->lastName, 'Wrong author');
        $this->assertEquals((object) ['foo' => 'bar'], $travel->content);

        foreach (['firstName', 'lastName', 'id', 'picture'] as $attr) {
            $this->assertObjectHasAttribute($attr, $author);
        }

        $this->apiClient->updateTravel($id, 'Two Towers', 'Before the Return of the King', ['pew' => 'boom']);
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
}
