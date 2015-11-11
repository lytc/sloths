<?php

namespace Sloths\Application\Console\Command\Db;

use Sloths\Application\Console\Command\Command;
use Sloths\Db\Connection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DropCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('db:drop')
            ->setDescription('Drop the database')
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

        $database = $this->getSloths()->database;

        # write connection
        $writeConnection = $database->getWriteConnection();
        $dbname = $this->drop($writeConnection);
        $output->writeln(sprintf('Database <comment>%s</comment> has been dropped', $dbname));

        $readConnection = $database->getReadConnection();
        if ($readConnection != $writeConnection) {
            $dbname = $this->drop($readConnection);
            $output->writeln(sprintf('Database <comment>%s</comment> has been dropped', $dbname));
        }
    }

    protected function drop(Connection $connection)
    {
        $dsn = $connection->getDsn();
        $username = $connection->getUsername();
        $password = $connection->getPassword();

        $pattern = '/dbname=([^;]+);/';
        preg_match($pattern, $dsn, $matches);
        $dbname = $matches[1];
        $dsn = preg_replace($pattern, '', $dsn);

        $pdo = new \PDO($dsn, $username, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);

        $pdo->exec("DROP DATABASE `$dbname`");

        return $dbname;
    }
}