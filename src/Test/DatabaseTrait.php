<?php
namespace Api\Test;

use Api\Application;
use Doctrine\DBAL\Migrations\Migration;
use Migrator\Migrator;
use PDO;

trait DatabaseTrait
{
    static protected function resetDatabase(Application $app)
    {
        foreach ($app['config']['db'] as $name => $db) {
            $pdo = new PDO(
                "pgsql:dbname=postgres;host={$db['host']}",
                $db['user'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $pdo->exec("SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '{$db['database']}'");
            $pdo->exec("DROP DATABASE IF EXISTS {$db['database']}");
            $pdo->exec("CREATE DATABASE {$db['database']} OWNER={$db['user']}");
            $app["db.$name.pdo"]->exec(file_get_contents(__DIR__ . "/../../db/{$name}/ext/postgis.sql"));

            $conf = $app["doctrine.migrations.configuration.$name"];
            $migration = new Migration($conf);
            $migration->migrate();
        }
    }
}
