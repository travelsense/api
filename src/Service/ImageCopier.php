<?php
namespace Api\Service;

use Exception;

class ImageCopier
{
    /**
     * @var int
     */
    private $timeout;

    /**
     * @var ImageLoader
     */
    private $image_loader;

    /**
     * ImageCopier constructor.
     * @param ImageLoader $image_loader
     * @param int         $timeout
     */
    public function __construct(
        ImageLoader $image_loader,
        int $timeout
    ) {
        $this->image_loader = $image_loader;
        $this->timeout = $timeout;
    }


    /**
     * Copy file from remote resource by url.
     *
     * @param string $from_url
     * @return string
     * @throws Exception
     */
    public function copyFrom(string $from_url): string
    {
        $context = stream_context_create(['http' => ['timeout' => $this->timeout]]);
        $stream = fopen($from_url, 'r', false, $context);
        if (!$stream) {
            throw new Exception("Could not open the file $from_url");
        }
        $link = $this->image_loader->upload($stream);
        fclose($stream);
        return $link;
    }
}
