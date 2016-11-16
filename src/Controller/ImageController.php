<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class ImageController
{
    use LoggerAwareTrait;

    private $allowed_mime_types = [];
    private $upload_dir;
    private $base_url;

    public function __construct(array $allowed_mime_types, string $upload_dir, string $base_url)
    {
        $this->allowed_mime_types = $allowed_mime_types;
        $this->upload_dir = $upload_dir;
        $this->base_url = $base_url;
    }

    public function upload(Request $request)
    {
        $tmp_file = tmpfile();
        stream_copy_to_stream($request->getContent(true), $tmp_file);
        fflush($tmp_file);
        $tmp_file_name = stream_get_meta_data($tmp_file)['uri'];
        $this->logger->debug($tmp_file_name);
        $tmp_file_type = mime_content_type($tmp_file_name);
        if (!in_array($tmp_file_type, $this->allowed_mime_types)) {
            throw new ApiException("Invalid mime type: $tmp_file_type", ApiException::VALIDATION);
        }
        $hash = sha1_file($tmp_file_name);
        $path = $this->getPath($hash);
        $dir = "{$this->upload_dir}/{$path}";
        $this->logger->debug("Creating dir: $dir");
        mkdir($dir, 0700, true);
        rename($tmp_file_name, "{$dir}/{$hash}");
        return [
            'url' => "{$this->base_url}/{$path}/{$hash}",
        ];
    }

    private function getPath(string $hash): string
    {
        $depth = 2;
        $length = 2;
        $path = [];
        for ($i = 0; $i < $depth * $length; $i += $length) {
            $path[] = substr($hash, $i, $length);
        }
        return implode('/', $path);
    }
}
