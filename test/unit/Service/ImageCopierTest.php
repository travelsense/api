<?php
namespace Api\Service;

use PHPUnit_Framework_TestCase;

class ImageCopierTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ImageStorage
     */
    private $image_storage;

    /**
     * @var ImageCopier
     */
    private $image_copier;

    public function setUp()
    {
        $this->image_storage = $this->createMock(ImageStorage::class);
    }

    public function testCopyFrom()
    {
        $this->image_storage->expects($this->once())
            ->method('upload')
            ->with($this->isType('resource'))
            ->willReturn('https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5');

        $this->image_copier = new ImageCopier($this->image_storage, 5);
        $link = $this->image_copier->copyFrom( __FILE__);
        $this->assertEquals(
            'https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5',
            $link
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not open /example.com/my-avatar.jpg
     */
    public function testCopyFromError()
    {
        $this->image_copier = new ImageCopier($this->image_storage, 5);
        $this->image_copier->copyFrom('/example.com/my-avatar.jpg');
    }
}
