<?php

namespace Sloths\Application\Console\Command\Route;

use Sloths\Application\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('route:list')->setAliases(['routes'])
            ->setDescription('List all routes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $router = $this->getSloths()->getRouter();

        $rows = [];

        foreach ($router as $index => $route) {
            $rows[] = [$index + 1, implode('|', $route->getMethods()), $route->getPattern()];
        }

        $table = new Table($output);
        $table
            ->setHeaders(array('#', 'METHOD', 'PATTERN'))
            ->setRows($rows)
        ;

        $table->render();
    }

}