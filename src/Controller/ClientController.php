<?php
namespace Api\Controller;

use Api\Exception\ApiException;

class ClientController
{
    /**
     * @param string $version
     * @return array
     */
    public function version(string $version): array
    {
        $regex = '/^(\d+)\.(\d+)\.(\d+)$/';
        if (preg_match($regex, $version, $matches)) {
            return [
                'version'   => $version,
                'supported' => true,
            ];
        }
        throw new ApiException('Unknown version', ApiException::RESOURCE_NOT_FOUND);
    }
}
