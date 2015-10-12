<?php

namespace Sloths\Application\Console\Command\Migration;

use Sloths\Application\Console\Command\Command;
use Sloths\Misc\Inflector;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RollbackCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('migration:rollback')
            ->setDescription('Rollback the migrations to the target steps. Default to 1')
            ->addOption(
                'steps',
                null,
                InputOption::VALUE_OPTIONAL,
                'Steps to rollback',
                1
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $steps = $input->getOption('steps');
        $migrator = $this->getSloths()->migrator;
        $namespace = $migrator->getNamespace();

        $migrator->addEventListeners([
            'rollback' => function($e, $migration) use (&$time, $output, $namespace) {
                $time = microtime(true);
                $output->writeln(sprintf('==> %s: rolling back', substr($migration['className'], strlen($namespace) + 1)));
            },
            'rolledback' => function($e, $migration) use (&$time, $output, $namespace) {
                $output->writeln(sprintf(
                    '==> %s: rolledback (%.4fs)',
                    substr($migration['className'], strlen($namespace) + 1),
                    (microtime(true) - $time)
                ));
                $output->writeln('');
            }
        ]);

        $migrator->rollback($steps);
    }
}