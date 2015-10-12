<?php

namespace Sloths\Application\Console\Command\Migration;

use Sloths\Application\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ListCommand extends Command
{
    protected function configure()
    {
        $this->setName('migration:list')->setAliases(['migrations'])
            ->setDescription('List all migrations')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = $this->getSloths()->migrator;
        $namespace = $migrator->getNamespace();
        $migrations = $migrator->listMigrations();


        $rows = [];

        foreach (array_values($migrations) as $index => $migration) {
            $rows[] = [
                $index + 1,
                $migration['migrated']? '✓' : '' ,
                $migration['version'],
                substr($migration['className'], strlen($namespace) + 1),
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(array('#', 'MIGRATED', 'VERSION', 'NAME'))
            ->setRows($rows)
        ;

        $table->render();
    }
}