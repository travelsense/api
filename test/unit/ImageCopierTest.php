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

    public function testCopyFrom()
    {
        $this->image_loader = $this->getMockBuilder(ImageLoader::class)
            ->setMethods(['upload'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->image_loader->expects($this->once())
            ->method('upload')
            ->willReturn('https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5');

        $fake_remote_uri = tempnam(sys_get_temp_dir(), 'image') . '.jpg';

        $this->image_copier = new ImageCopier($this->image_loader);

        $link = @$this->image_copier->copyFrom($fake_remote_uri);

        $this->assertEquals(
            'https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5',
            $link
        );
    }
}
