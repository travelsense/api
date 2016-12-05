<?php
namespace Test;

use Api\Test\ApplicationTestCase;

class ImageUploadTest extends ApplicationTestCase
{
    /**
     * @expectedException \HopTrip\ApiClient\ApiClientException
     * @expectedExceptionMessage Invalid mime type: text/x-php
     */
    public function testInvalidMimeType()
    {
        $this->appExpectTokens(['testtoken' => 1]);
        $api = $this->createApiClient('testtoken');
        $api->uploadImage(file_get_contents(__FILE__));
    }

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
        $file = $this->app['config']['image_upload']['dir'] . '/b2/0e/b20e6e912ef015c7389230a9b8c0ac6959c37fda';
        if (file_exists($file)) {
            unlink($file);
        }
        $api = $this->createApiClient('testtoken');

        for ($i = 0; $i < 2; $i++) { // loop to ensure the call is idempotent
            $response = $api->uploadImage(file_get_contents(__DIR__ . '/stub/pic.jpg'));
            $this->assertEquals(
                'https://static.hoptrip.us/b2/0e/b20e6e912ef015c7389230a9b8c0ac6959c37fda',
                $response->url
            );
            $this->assertFileEquals(
                __DIR__ . '/stub/pic.jpg',
                $file
            );
        }
    }
}
