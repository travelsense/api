<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use DateTime;

abstract class ApiController
{
    const DATETIME_FORMAT = DateTime::W3C;
}
