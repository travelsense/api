<?php
namespace Api\Controller;

use Api\Service\ImageLoader;
use Symfony\Component\HttpFoundation\Request;

class ImageController
{
    /**
     * @var ImageLoader
     */
    private $image_loader;

    public function __construct(ImageLoader $image_loader)
    {
        $this->image_loader = $image_loader;
    }

    public function upload(Request $request): array
    {
        return [
            'url' => $this->image_loader->upload($request->getContent(true))
        ];
    }
}
