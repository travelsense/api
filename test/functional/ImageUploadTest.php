<?php
namespace Test;

use Api\Test\ApplicationTestCase;

class ImageUploadTest extends ApplicationTestCase
{
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionCode 401
     */
    public function testUnauthorizedUser()
    {
        $api = $this->createApiClient();
        $api->uploadImage(file_get_contents(__FILE__));
    }

    public function testImageUpload()
    {
        $this->appExpectTokens(['testtoken' => 1]);
        $this->app['image_storage']
            ->expects($this->once())
            ->method('upload')
            ->with($this->isType('resource'))
            ->willReturn('https://exmple.com/pic');
        $api = $this->createApiClient('testtoken');

        $response = $api->uploadImage('my image contents');
        $this->assertEquals(
            'https://exmple.com/pic',
            $response->url
        );
    }
}
