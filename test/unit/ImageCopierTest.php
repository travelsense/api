<?php
namespace Api;

use Api\Service\ImageCopier;
use Api\Service\ImageLoader;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
            ->with($this->callback(function ($value) {
                if (getType($value)==='resource') {
                    return $value;
                }
                return false;
            }))
            ->willReturn('https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5');

        $this->image_copier = new ImageCopier($this->image_loader, 5);

        $link = $this->image_copier->copyFrom('./test/unit/ImageCopierTest.php');

        $this->assertEquals(
            'https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5',
            $link
        );
    }

    public function testCopyFromError()
    {
        $this->image_loader = $this->getMockBuilder(ImageLoader::class)
            ->setMethods(['upload'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->image_copier = new ImageCopier($this->image_loader, 5);

        $fake_remote_uri = '/example.com/my-avatar.jpg';

        try {
            @$this->image_copier->copyFrom($fake_remote_uri);
            $this->fail();
        } catch (FileException $e) {
            $this->assertEquals("Could not open the file $fake_remote_uri", $e->getMessage());
        }
    }
}
