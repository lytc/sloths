<?php

namespace Sloths\Application\Console\Command\Migration;

use Sloths\Application\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VersionCommand extends Command
{
    protected function configure()
    {
        $this->setName('migration:version')
            ->setDescription('Get migration version')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = $this->getSloths()->migrator;
        $version = $migrator->getLastMigratedVersion();
        $output->writeln(sprintf('Migration version: <comment>%s</comment>', $version?: 0));
    }
}