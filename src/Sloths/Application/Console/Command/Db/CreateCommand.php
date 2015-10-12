<?php

namespace Sloths\Application\Console\Command\Db;

use Sloths\Application\Console\Command\Command;
use Sloths\Db\Connection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('db:create')
            ->setDescription('Create the database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $database = $this->getSloths()->database;

        # write connection
        $writeConnection = $database->getWriteConnection();
        $dbname = $this->create($writeConnection);
        $output->writeln(sprintf('Database <comment>%s</comment> has been created', $dbname));

        $readConnection = $database->getReadConnection();
        if ($readConnection != $writeConnection) {
            $dbname = $this->create($readConnection);
            $output->writeln(sprintf('Database <comment>%s</comment> has been created', $dbname));
        }
    }

    protected function create(Connection $connection)
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

        $pdo->exec("CREATE DATABASE `$dbname`");

        return $dbname;
    }
}