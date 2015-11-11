<?php

namespace Sloths\Application\Console\Command\Migration;

use Sloths\Application\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ResetCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('migration:reset')
            ->setDescription('Rollback all migrations and migrate again')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<error>All data will be lost!</error>');
        $helper = $this->getHelper('question');
        $confirm = new ConfirmationQuestion('Are you sure?<comment>[yes/no]</comment>: ', false, '/^yes$/i');

        if (!$helper->ask($input, $output, $confirm)) {
            return;
        }

        $rollbackCommand = $this->getApplication()->get('migration:rollback');
        $migrateCommand = $this->getApplication()->get('migration:migrate');

        $rollbackCommand->run(new ArrayInput([
            'command' => 'migration:rollback',
            '--steps' => -1
        ]), $output);

        $migrateCommand->run($input, $output);
    }
}