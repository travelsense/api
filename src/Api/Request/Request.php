<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 11/11/15
 * Time: 6:10 PM
 */

namespace Api\Request;

class Request
{
    /**
     * Set properties
     * @param array $payload
     * @return void
     */
    function init(array $payload)
    {
        foreach ($payload as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }
}