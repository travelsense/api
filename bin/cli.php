<?php
/**
 * CLI helpers
 */

/**
 * Terminate
 * @param string $msg
 * @param int $code
 */
function terminate($msg, $code)
{
    echo $msg;
    exit($code);
}

/**
 * Run a command, terminate if command was unsuccessful
 * @param string $cmd
 */
function run($cmd)
{
    passthru($cmd, $ret);
    if ($ret !== 0) {
        terminate("[COMMAND FAILED WITH EXIT CODE $ret]\n", 1);
    }
}