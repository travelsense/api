<?php
namespace Api\Service;

use Api\Exception\ApiException;

class ImageStorage
{
    /**
     * @var array
     */
    private $allowed_mime_types = [];

    /**
     * @var string
     */
    private $upload_dir;

    /**
     * @var string
     */
    private $base_url;

    /**
     * @var int
     */
    private $size_limit = 4 * 1024 * 1024;

    public function __construct(array $allowed_mime_types, string $upload_dir, string $base_url)
    {
        $this->allowed_mime_types = $allowed_mime_types;
        $this->upload_dir = $upload_dir;
        $this->base_url = $base_url;
    }

    /**
     * @param resource $stream
     * @return string
     */
    public function upload($stream): string
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Resource expected');
        }
        $tmp_file = tmpfile();
        $actual_size = stream_copy_to_stream($stream, $tmp_file, $this->size_limit);
        if ($actual_size >= $this->size_limit) {
            throw new \LengthException("File is too big");
        }
        fflush($tmp_file);
        $tmp_file_name = stream_get_meta_data($tmp_file)['uri'];
        $tmp_file_type = mime_content_type($tmp_file_name);
        if (!in_array($tmp_file_type, $this->allowed_mime_types)) {
            throw new ApiException("Invalid mime type: $tmp_file_type", ApiException::VALIDATION);
        }
        $hash = sha1_file($tmp_file_name);
        $path = $this->getPath($hash);
        $dir = "{$this->upload_dir}/{$path}";
        @mkdir($dir, 0700, true);
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \RuntimeException("Unable to create dir $dir");
        }
        rename($tmp_file_name, "{$dir}/{$hash}");
        fclose($tmp_file);
        return "{$this->base_url}/{$path}/{$hash}";
    }

    /**
     * @param int $size_limit bytes
     */
    public function setSizeLimit(int $size_limit)
    {
        $this->size_limit = $size_limit;
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
