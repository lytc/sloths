<?php

namespace Sloths\Application\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ServiceListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('service:list')->setAliases(['services'])
            ->setDescription('List all application services')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceManager = $this->getSloths()->getServiceManager();
        $rows = [];

        foreach ($serviceManager->getAll() as $index => $name) {
            $rows[] = [$index + 1, $name, get_class($serviceManager->get($name))];
        }

        $table = new Table($output);
        $table
            ->setHeaders(array('#', 'NAME', 'CLASS'))
            ->setRows($rows)
        ;

        $table->render();
    }
}