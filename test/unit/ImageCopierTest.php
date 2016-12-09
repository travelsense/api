<?php
namespace Api;

use Api\Service\ImageCopier;
use Api\Service\ImageLoader;
use PHPUnit_Framework_TestCase;

class ImageCopierTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ImageLoader
     */
    private $image_loader;

    /**
     * @var ImageCopier
     */
    private $image_copier;

    public function setUp()
    {
        $this->image_loader = $this->createMock(ImageLoader::class);
    }

    public function testCopyFrom()
    {
        $this->image_loader->expects($this->once())
            ->method('upload')
            ->with($this->isType('resource'))
            ->willReturn('https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5');

        $this->image_copier = new ImageCopier($this->image_loader, 5);

        $link = $this->image_copier->copyFrom( __FILE__);

        $this->assertEquals(
            'https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5',
            $link
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCopyFromError()
    {
        $this->image_copier = new ImageCopier($this->image_loader, 5);

        $fake_remote_uri = '/example.com/my-avatar.jpg';

        $this->image_copier->copyFrom($fake_remote_uri);
    }
}
