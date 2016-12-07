<?php
namespace Api\Service;

use GuzzleHttp\Client;

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
        ImageLoader $image_loader,
        Client $client,
        string $dir,
        string $file_name
    ) {
        $this->image_loader = $image_loader;
        $this->guzzle = $client;
        $this->dir = $dir;
        $this->file_name = $file_name;
    }

    /**
     * Copy file from remote resource by url.
     *
     * @param string $from_url
     * @return string
     */
    public function copyFrom(string $from_url): string
    {
        @mkdir($this->dir, 0700, true);
        $file_path = $this->dir . '/' . date('YmdHis') . uniqid() . $this->file_name;
        $this->guzzle->request('GET', $from_url, ['sink' => fopen($file_path, 'w')]);
        $resource = fopen($file_path, 'r');
        $link = $this->image_loader->upload($resource);
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        return $link;
    }
}
