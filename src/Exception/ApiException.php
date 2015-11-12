<?php
/**
 * Exception visible to an API user. To be converted to a JSON response..
 */

namespace Exception;

use Exception;
use LogicException;

class ApiException extends Exception
{
    // State related
    const USER_EXISTS = 101;

    // Format related
    const PASSWORD_TOO_SHORT = 201;
    const INVALID_EMAIL = 202;
    const JSON_EXPECTED = 203;

    // Auth related
    const AUTH_REQUIRED = 301;
    const INVALID_EMAIL_PASSWORD = 302;

    // Mapping to HTTP code and message
    private static $map = [
        self::USER_EXISTS => [403, 'User exists'],
        self::PASSWORD_TOO_SHORT => [403, 'Password too short'],
        self::INVALID_EMAIL => [403, 'invalid email'],
        self::AUTH_REQUIRED => [401, 'User exists'],
        self::INVALID_EMAIL_PASSWORD => [401, 'Invalid email or password'],
        self::JSON_EXPECTED => [401, 'JSON Content-type expected'],
    ];

    private $httpCode;

    public function __construct($message, $code, Exception $previous, $httpCode = 500)
    {
        parent::__construct($message, $code, $previous);
        $this->httpCode = $httpCode;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param int $code
     * @return ApiException
     * @throws LogicException
     */
    public static function create($code)
    {
        if (isset(self::$map[$code])) {
            list($httpCode, $message) = self::$map[$code];
            return new self($message, $code, null, $httpCode);
        }
        throw new LogicException("Unknown exception code $code");
    }
}