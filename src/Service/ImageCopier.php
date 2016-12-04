<?php
namespace Api\Service;

use GuzzleHttp\Client;

class ImageCopier
{
    /**
     * @var ImageLoader
     */
    private $image_loader;

    private $guzzle;

    public function __construct(
        ImageLoader $image_loader,
        Client $client
    ) {
        $this->image_loader = $image_loader;
        $this->guzzle = $client;
    }

    public function copyFrom(string $from_url): string
    {
        return $this->image_loader->upload($this->guzzle->request('GET', $from_url, ['stream' => true]));
    }
}
