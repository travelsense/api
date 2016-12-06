<?php
namespace Api\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\StreamWrapper;

class ImageCopier
{
    /**
     * @var ImageLoader
     */
    private $image_loader;

    /**
     * ImageCopier constructor.
     * @param ImageLoader $image_loader
     */
    public function __construct(
        ImageLoader $image_loader
    ) {
        $this->image_loader = $image_loader;
    }

    /**
     * Copy file from remote resource by url.
     *
     * @param string $from_url
     * @return string
     */
    public function copyFrom(string $from_url): string
    {
        $stream = \GuzzleHttp\Psr7\stream_for($from_url);
        $resource = StreamWrapper::getResource($stream);
        $link = $this->image_loader->upload($resource);
        return $link;
    }
}
