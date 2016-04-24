<?php
namespace Api\Migrator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    /**
     * @param OutputInterface $output
     * @param string          $name
     * @param string          $version
     */
    protected function printStatus(OutputInterface $output, string $name, string $version)
    {
        $output->writeln(
            "Database [ <fg=cyan;options=bold>$name</> ] is at version [ <fg=cyan;options=bold>$version</> ]"
        );
    }
}
