<?php
namespace Api\Service;

use GuzzleHttp\Client;

class ImageSaver
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $file;

    public function __construct(string $dir, string $file)
    {
        $this->dir = $dir;
        $this->file = $file;
    }

    public function save($from_url)
    {
        $client = new Client();
        @mkdir($this->dir, 0700, true);
        $file_paht = $this->dir . $this->file;
        $client->request('GET', $from_url, ['sink' => fopen($file_paht, 'w')]);
        $resource = fopen($file_paht, 'r');
        echo 'SAVE';
        var_dump($resource);
        return $resource;
    }
}
