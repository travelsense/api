<?php

/**
 * Array helper
 */
class A
{
    /**
     * Get array value if exists, otherwise return default
     * @param array $a
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    static public function get(array $a, $key, $default = null)
    {
        return isset($a[$key]) ? $a[$key] : $default;
    }

}