<?php
/**
 * Exception visible to an API user. To be converted to a JSON response..
 */

namespace Exception;

use Exception\ApiException\Forbidden;

class ApiException extends \Exception
{
    // State related
    const USER_EXISTS = 101;

    // Format related
    const PASSWORD_TOO_SHORT = 201;
    const INVALID_EMAIL = 202;

    /**
     * @param $type
     * @return ApiException
     */
    public static function create($type)
    {
        switch ($type) {
            case self::USER_EXISTS:
                return new Forbidden('User exists');
            case self::PASSWORD_TOO_SHORT:
                return new Forbidden('Password too short');
            case self::INVALID_EMAIL:
                return new Forbidden('Invalid email');
        }
        throw new \LogicException("Unknown exception type $type");
    }
}