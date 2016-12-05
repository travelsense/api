<?php
namespace Api;

use Api\Service\ImageCopier;
use PHPUnit_Framework_TestCase;

class ImageCopierTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ImageCopier
     */
    private $service;

    public function setUp()
    {
        $app = new Application('test');

        $this->service = $app['image_copier'];
    }

    public function testCopyFrom()
    {
        $link = $this->service->copyFrom('https://avatarko.ru/img/kartinka/13/kot_ochki_12879.jpg');
        $this->assertEquals(
            'https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5',
            $link
        );
    }
}
