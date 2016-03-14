<?php
namespace Test;

use Api\Test\FunctionalTestCase;

class TravelCRUDTest extends FunctionalTestCase
{
    public function testTravelCreationAndRetrieval()
    {
        $this->createAndLoginUser();
        $id = $this->apiClient->createTravel('Hobbit', 'There and back again');
        $this->assertGreaterThan(0, $id);
        $travel = $this->apiClient->getTravel($id);
        $author = $travel->author;
        $this->assertEquals('Hobbit', $travel->title);
        $this->assertEquals('There and back again', $travel->description);

        $this->assertEquals('Pushkin', $author->lastName, 'Wrong author');

        foreach (['firstName', 'lastName', 'id', 'picture'] as $attr) {
            $this->assertObjectHasAttribute($attr, $author);
        }
    }
}
