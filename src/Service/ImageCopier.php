<?php
namespace Api\Service;

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
        $stream = fopen($from_url, 'r');
        stream_set_timeout($stream, 5);
        var_dump($stream);
        $link = $this->image_loader->upload($stream);
        fclose($stream);
        var_dump($link);
        return $link;
    }
}
