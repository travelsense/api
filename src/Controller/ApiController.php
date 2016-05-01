<?php
namespace Api\Controller;

use DateTime;
use Psr\Log\LoggerAwareTrait;

abstract class ApiController
{
    use LoggerAwareTrait;

    const DATETIME_FORMAT = DateTime::W3C;
}
