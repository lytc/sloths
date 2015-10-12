<?php

namespace Sloths\Application\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnvCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('env')
            ->setDescription('Get the current application environment')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(
            'The current application environment: <comment>%s</comment>',
            $this->getSloths()->getEnv()
        ));
    }
}