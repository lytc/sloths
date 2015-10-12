<?php

namespace Sloths\Application\Console\Command\Db;

use Sloths\Application\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class SeedCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('db:seed')
            ->setDescription('Seed the database')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Do not ask to confirm'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('force')) {
            $helper = $this->getHelper('question');
            $confirm = new ConfirmationQuestion('Are you sure?<comment>[yes/no]</comment>: ', false, '/^yes$/i');

            if (!$helper->ask($input, $output, $confirm)) {
                return;
            }
        }

        $connection = $this->getSloths()->database->getWriteConnection();
        $connection->addEventListener('run', function($e, $sql) use ($output) {
            $output->writeln(sprintf('=> <comment>%s</comment>', $sql));
        });

        $output->writeln('==> Seeding');
        $time = microtime(true);

        $sloths = $this->getSloths();
        $file = $sloths->getPath('_shared') . '/db/seeds.php';

        call_user_func(\Closure::bind(function() use ($file) {
            require $file;
        }, $sloths, $sloths));

        $output->writeln(sprintf('==> Seeded (%.4fs)', microtime(true) - $time));
    }
}