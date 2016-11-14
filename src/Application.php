<?php
namespace Api;

use HopTrip\SilexApp\Config;

class Application extends \Silex\Application
{
    public function __construct(string $env = null)
    {
        parent::__construct();
        (new Config(__DIR__ . '/../app/config'))
            ->configure($this, $env);
    }
}
