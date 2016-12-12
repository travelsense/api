<?php
namespace Api\Controller;

use Api\Service\ImageStorage;
use Symfony\Component\HttpFoundation\Request;

class ImageController
{
    /**
     * @var ImageStorage
     */
    private $image_storage;

    public function __construct(ImageStorage $image_loader)
    {
        $this->image_storage = $image_loader;
    }

    public function upload(Request $request): array
    {
        return [
            'url' => $this->image_storage->upload($request->getContent(true))
        ];
    }
}
