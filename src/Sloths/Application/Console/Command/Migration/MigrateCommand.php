<?php

namespace Sloths\Application\Console\Command\Migration;

use Sloths\Application\Console\Command\Command;
use Sloths\Misc\Inflector;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('migration:migrate')
            ->setDescription('Run all pending migrations')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = $this->getSloths()->migrator;
        $namespace = $migrator->getNamespace();

        $migrator->addEventListeners([
            'migrate' => function($e, $migration) use (&$time, $output, $namespace) {
                $time = microtime(true);
                $output->writeln(sprintf('==> %s: migrating', substr($migration['className'], strlen($namespace) + 1)));
            },
            'migrated' => function($e, $migration) use (&$time, $output, $namespace) {
                $output->writeln(sprintf(
                    '==> %s: migrated (%.4fs)',
                    substr($migration['className'], strlen($namespace) + 1),
                    (microtime(true) - $time)
                ));
                $output->writeln('');
            }
        ]);

        $migrator->migrate();
    }
}