<?php
/**
 * Exception visible to an API user. To be converted to a JSON response..
 */

namespace Api\Exception;

use RuntimeException;

class ApiException extends RuntimeException
{
    // State related
    const USER_EXISTS = 1000;

    // Auth related
    const AUTH_REQUIRED = 2000;
    const INVALID_EMAIL_PASSWORD = 2100;
    const INVALID_TOKEN = 2200;

    // Validation
    const VALIDATION = 3000;

    // Not found
    const RESOURCE_NOT_FOUND = 4000;

    // Access violation
    const ACCESS_DENIED = 5000;
}
