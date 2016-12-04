<?php
namespace Api\Service;

use GuzzleHttp\Client;

class ImageCopier
{
    /**
     * @var ImageLoader
     */
    private $image_loader;

    public function __construct(ImageLoader $image_loader)
    {
        $this->image_loader = $image_loader;
    }

    function copyFrom(string $from_url): string
    {
        $guzzle = new Client();
        return $this->image_loader->upload($guzzle->request('GET', $from_url, ['stream' => true]));
    }
}
