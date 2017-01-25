<?php
/**
 * Created by PhpStorm.
 * User: Olha
 * Date: 1/25/2017
 * Time: 10:07 AM
 */

namespace Api\Model;

trait HasUuidTrait
{
    /**
     * @var string
     */
    private $uuid;

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return $this
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * Generate UUID
     * @return string
     */
    public static function generateUuid(): string
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
}
