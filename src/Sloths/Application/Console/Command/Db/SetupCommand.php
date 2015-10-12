<?php

namespace Sloths\Application\Console\Command\Db;

use Sloths\Application\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class SetupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('db:setup')
            ->setDescription(
                'Create database, load the schema and load the seed data. You should drop database first (<comment>db:drop</comment>)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $confirm = new ConfirmationQuestion('Are you sure?<comment>[yes/no]</comment>: ', false, '/^yes$/i');

        if (!$helper->ask($input, $output, $confirm)) {
            return;
        }

        $app = $this->getApplication();

        $commands = [
            [$app->get('db:create'), [$input, $output]],
            [$app->get('migration:setup'), [$input, $output]],
            [$app->get('migration:migrate'), [$input, $output]],
            [$app->get('model:sync'), [$input, $output]],
            [$app->get('db:seed'), [new ArrayInput(['command' => 'db:create', '--force' => true]), $output]],
        ];

        foreach ($commands as $item) {
            $command = $item[0];
            $args = $item[1];

            $output->writeln($command->getDescription());
            call_user_func_array([$command, 'run'], $args);
            $output->writeln('');
        }
    }
}