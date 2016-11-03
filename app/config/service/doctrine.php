<?php
/**
 * Doctrine configuration.
 * @var $app Api\Application
 */

use Api\Application as ApiApp;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\MigrationsVersion;
use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

$app->register(new \Silex\Provider\DoctrineServiceProvider(), [
    'dbs.options' => [
        'main' => [
            'dbname'   => $app['config']['db']['main']['database'],
            'user'     => $app['config']['db']['main']['user'],
            'password' => $app['config']['db']['main']['password'],
            'host'     => $app['config']['db']['main']['host'],
            'driver'   => 'pdo_pgsql',
        ],
    ],
]);

$app['doctrine.migrations.configuration.main'] = function (ApiApp $app) {
    $conf = new Configuration($app['dbs']['main']);
    $conf->setMigrationsNamespace('Api\\DB\\Migration');
    $conf->setMigrationsDirectory(__DIR__ . '/../../../db/main');
    $conf->setMigrationsTableName('__doctrine_migration_versions');
    return $conf;
};

$app['doctrine.migrations.app.main'] = function (ApiApp $app) {
    $helperSet = new HelperSet();
    $helperSet->set(new QuestionHelper(), 'question');
    $helperSet->set(new ConfigurationHelper($app['dbs']['main'], $app['doctrine.migrations.configuration.main']));

    $cli = new Application('Doctrine Migrations', MigrationsVersion::VERSION());
    $cli->setCatchExceptions(true);
    $cli->setHelperSet($helperSet);
    $cli->addCommands([
        // Migrations Commands
        new ExecuteCommand(),
        new GenerateCommand(),
        new LatestCommand(),
        new MigrateCommand(),
        new StatusCommand(),
        new VersionCommand(),
    ]);
    if ($helperSet->has('em')) {
        $cli->add(new DiffCommand());
    }
    return $cli;
};
