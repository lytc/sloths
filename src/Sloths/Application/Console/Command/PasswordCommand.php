<?php

namespace Sloths\Application\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PasswordCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('password')
            ->setDescription('Encodes a password')
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'The plain password to encode'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $password = $input->getArgument('password');

        $output->writeln(sprintf(
            'Password encoded: <comment>%s</comment>',
            $this->getSloths()->password->hash($password)
        ));
    }
}