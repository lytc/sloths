<?php

namespace Sloths\Application\Console\Command\Migration;

use Sloths\Application\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{
    protected function configure()
    {
        $this->setName('migration:setup')
            ->setDescription('Setup migration')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = $this->getSloths()->migrator;
        $table = $migrator->getTable();
        $sql = "
            CREATE TABLE `$table`(
              `version` VARCHAR(14) NOT NULL
            )
        ";
        $migrator->getConnection()->exec($sql);

        $output->writeln(sprintf('Table has been created: <comment>%s</comment>', $table));
    }
}