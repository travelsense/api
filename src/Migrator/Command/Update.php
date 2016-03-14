<?php
namespace Api\Migrator\Command;

use Api\Migrator\ConsoleApp;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Update
 * @package Api\Migrator\Command
 * @method ConsoleApp getApplication
 */
class Update extends AbstractCommand
{
    const LATEST = 'latest';

    protected function configure()
    {
        $this
            ->setName('update')
            ->setDescription('Update database schema to the given version')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Database name',
                'main'
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'Target version',
                self::LATEST
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $target = $input->getArgument('target');
        if ($target === self::LATEST) {
            $target = null;
        } else {
            $target = (int) $target;
        }
        $migrator = $this->getApplication()->getMigrator($name);
        $migrator->upgrade($target);
        $this->printStatus($output, $name, $migrator->getVersion());
    }
}
