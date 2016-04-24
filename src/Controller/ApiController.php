<?php
namespace Api\Controller;

use DateTime;

abstract class ApiController
{
    const DATETIME_FORMAT = DateTime::W3C;
}
