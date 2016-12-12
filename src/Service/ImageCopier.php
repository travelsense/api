<?php
namespace Api\Service;

class ImageCopier
{
    /**
     * @var int
     */
    private $timeout;

    /**
     * @var ImageStorage
     */
    private $image_storage;

    /**
     * ImageCopier constructor.
     * @param ImageStorage $image_loader
     * @param int          $timeout
     */
    public function __construct(
        ImageStorage $image_loader,
        int $timeout
    ) {
        $this->image_storage = $image_loader;
        $this->timeout = $timeout;
    }


    /**
     * Copy file from remote resource by url.
     *
     * @param string $from_url
     * @return string
     * @throws \RuntimeException
     */
    public function copyFrom(string $from_url): string
    {
        $context = stream_context_create(['http' => ['timeout' => $this->timeout]]);

        if (!($stream = @fopen($from_url, 'r', false, $context))) {
            throw new \RuntimeException("Could not open $from_url");
        }

        $link = $this->image_storage->upload($stream);
        fclose($stream);
        return $link;
    }
}
