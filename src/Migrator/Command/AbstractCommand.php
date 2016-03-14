<?php
namespace Api\Migrator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand extends Command
{
    protected function printStatus(OutputInterface $output, $name, $version)
    {
        $output->writeln(
            "Database [ <fg=cyan;options=bold>$name</> ] is at version [ <fg=cyan;options=bold>$version</> ]"
        );
    }
}
