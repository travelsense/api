<?php
namespace Api\Model\Travel;

class TravelTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterSetter()
    {
        $travel = new Travel();
        $travel->setAppVersion('v42');
        $this->assertEquals('v42', $travel->getAppVersion());
    }
}
