<?php
namespace Api\Test;

use Api\Application;
use Doctrine\DBAL\Migrations\Migration;
use PDO;

trait DatabaseTrait
{
    static protected function resetDatabase(Application $app)
    {
        foreach ($app['config']['db'] as $name => $db) {
            $admin_pdo = new PDO(
                "pgsql:dbname=postgres;host={$db['host']}",
                $db['user'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $admin_pdo->exec("
              SELECT pg_terminate_backend(pid) 
              FROM pg_stat_activity 
              WHERE datname = '{$db['database']}'
            ");
            $admin_pdo->exec("DROP DATABASE IF EXISTS {$db['database']}");
            $admin_pdo->exec("CREATE DATABASE {$db['database']} OWNER={$db['user']}");

            $conf = $app["doctrine.migrations.configuration.$name"];
            $migration = new Migration($conf);
            $migration->migrate();
        }
    }
}
