<?php

namespace Api\Test;

use PDO;

trait DBTestingTrait
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
