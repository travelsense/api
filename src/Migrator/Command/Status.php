<?php
namespace Api\Migrator\Command;

use Api\Migrator\ConsoleApp;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Status
 * @package Migrator\Command
 *
 * @method ConsoleApp getApplication
 */
class Status extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('Database status')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Database name',
                'main'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $migrator = $this->getApplication()->getMigrator($name);
        $upgrades = $migrator->getAvailableUpgrades();
        $current = $migrator->getVersion();
        $this->printStatus($output, $name, $current);

        if ($upgrades) {
            $output->writeln("Available upgrades:");
            foreach ($upgrades as $ver => $file) {
                $output->writeln(" - <fg=green>$ver</> ($file)");
            }
        } else {
            $output->writeln('<fg=green>UP TO DATE</>');
        }
    }
}
