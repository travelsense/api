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
    const USER_EXISTS = 100;

    // Auth related
    const AUTH_REQUIRED = 200;
    const INVALID_EMAIL_PASSWORD = 201;

    const VALIDATION = 300; // Used in ValidationException

    // Mapping to HTTP code and message
    private static $map = [
        self::USER_EXISTS => [403, 'User exists'],
        self::AUTH_REQUIRED => [401, 'Authentication required'],
        self::INVALID_EMAIL_PASSWORD => [401, 'Invalid email or password'],
    ];

    private $httpCode;

    public function __construct($message, $code, Exception $previous = null, $httpCode = 500)
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