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
     * @var Client
     */
    private $guzzle;

    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $file_name;

    /**
     * ImageCopier constructor.
     * @param ImageLoader $image_loader
     * @param Client      $client
     * @param string      $dir
     * @param string      $file_name
     */
    public function __construct(
        ImageLoader $image_loader
//        Client $client,
//        string $dir,
//        string $file_name
    ) {
        $this->image_loader = $image_loader;
//        $this->guzzle = $client;
//        $this->dir = $dir;
//        $this->file_name = $file_name;
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
