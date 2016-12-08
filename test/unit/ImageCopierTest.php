<?php
namespace Api;

use Api\Service\ImageCopier;
use Api\Service\ImageLoader;
use PHPUnit_Framework_TestCase;

class ImageCopierTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ImageCopier
     */
    private $image_copier;

    public function setUp()
    {
        $this->image_copier = $this->getMockBuilder(ImageCopier::class)
            ->setMethods(['copyFrom'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testCopyFrom()
    {
        $this->image_copier->expects($this->once())
            ->method('copyFrom')
            ->with('http://example.com/my-avatar.jpg')
            ->willReturn('https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5');

        $link = $this->image_copier->copyFrom('http://example.com/my-avatar.jpg');
        var_dump($link);
        $this->assertEquals(
            'https://static.hoptrip.us/36/43/36439437709f38e3800e7d08504626b170d651d5',
            $link
        );
    }
}
