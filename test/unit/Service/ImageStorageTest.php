<?php
namespace Api\Service;

use PHPUnit\Framework\TestCase;

class ImageStorageTest extends TestCase
{
    private $upload_dir = '/tmp/images';

    /**
     * @expectedException \Api\Exception\ApiException
     * @expectedExceptionMessage Invalid mime type: text/x-php
     */
    public function testInvalidMimeType()
    {
        $storage = new ImageStorage(['image/jpeg'], $this->upload_dir, 'https://example.com');
        $storage->upload(fopen(__FILE__, 'r'));
    }

    /**
     * @expectedException \LengthException
     * @expectedExceptionMessage File is too big
     */
    public function testFileSize()
    {
        $storage = new ImageStorage(['image/jpeg'], $this->upload_dir, 'https://example.com');
        $storage->setSizeLimit(1);
        $storage->upload(fopen(__FILE__, 'r'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to create dir
     */
    public function testDirNotWritable()
    {
        $storage = new ImageStorage(['image/jpeg'], __FILE__, 'https://example.com');
        $storage->upload(fopen(__DIR__ . '/stub/pic.jpg', 'r'));
    }

    public function testImageUpload()
    {
        $storage = new ImageStorage(['image/jpeg'], $this->upload_dir, 'https://example.com');

        $file = $this->upload_dir . '/b2/0e/b20e6e912ef015c7389230a9b8c0ac6959c37fda';
        if (file_exists($file)) {
            unlink($file);
        }

        for ($i = 0; $i < 2; $i++) { // loop to ensure the call is idempotent
            $url = $storage->upload(fopen(__DIR__ . '/stub/pic.jpg', 'r'));
            $this->assertEquals(
                'https://example.com/b2/0e/b20e6e912ef015c7389230a9b8c0ac6959c37fda',
                $url
            );
            $this->assertFileEquals(
                __DIR__ . '/stub/pic.jpg',
                $file
            );
        }
    }
}
