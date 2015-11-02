<?php
/**
 * User: f3ath
 * Date: 11/1/15
 * Time: 4:41 PM
 */

namespace Test;

use PDO;

trait DBTesting
{
    protected function setUpDatabase(PDO $pdo, $database)
    {
        $pdo->exec(file_get_contents(__DIR__."/../../schema/$database.install.sql"));
    }

    protected function tearDownDatabase(PDO $pdo, $database)
    {
        $pdo->exec(file_get_contents(__DIR__."/../../schema/$database.uninstall.sql"));
    }
}