<?php
/**
 * Exception visible to an API user. To be converted to a JSON response..
 */

namespace Api\Exception;

use Exception;
use LogicException;
use Symfony\Component\HttpFoundation\Response;

class ApiException extends Exception
{
    // State related
    const USER_EXISTS = 100;

    // Auth related
    const AUTH_REQUIRED = 200;
    const INVALID_EMAIL_PASSWORD = 201;
    const INVALID_TOKEN = 202;

    // Validation
    const VALIDATION = 300; // Input data validation errors

    // Not found
    const RESOURCE_NOT_FOUND = 400;
    const ACCESS_DENIED = 403;

    // Access violation
    const ACCESS_DENIED = 500;

    // Mapping to HTTP code and message
    private static $map = [
        self::USER_EXISTS => [Response::HTTP_FORBIDDEN, 'User exists'],
        self::AUTH_REQUIRED => [Response::HTTP_UNAUTHORIZED, 'Authentication required'],
        self::INVALID_EMAIL_PASSWORD => [Response::HTTP_UNAUTHORIZED, 'Invalid email or password'],
        self::INVALID_TOKEN => [Response::HTTP_UNAUTHORIZED, 'Invalid or expired auth token'],
        self::RESOURCE_NOT_FOUND => [Response::HTTP_NOT_FOUND, 'Resource not found'],
        self::ACCESS_DENIED => [Response::HTTP_FORBIDDEN, 'Access denied'],
    ];

    private $httpCode;

    /**
     * ApiException constructor.
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     * @param int $httpCode
     */
    public function __construct(
        $message,
        $code,
        Exception $previous = null,
        $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ) {
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
