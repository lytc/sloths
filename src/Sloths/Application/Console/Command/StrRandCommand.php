<?php

namespace Sloths\Application\Console\Command;

use Sloths\Misc\StringUtils;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class StrRandCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('str:rand')
            ->setDescription('Generate a random string')
            ->addArgument(
                'length',
                InputArgument::REQUIRED,
                'The length of the random string'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $length = $input->getArgument('length');

        if (!is_numeric($length) || (int) $length != $length || $length < 1) {
            $output->writeln(sprintf('<error>Length must be a number greater than 0. %s given.</error>', $length));
            return;
        }

        $types = array(
            1   => 'Alpha lowercase',
            2   => 'Alpha upper case',
            4   => 'Numeric',
            8   => 'Special chars',
            15  => 'All',
        );
        
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select random types. Multiple type separate by commas.',
            array_values($types),
            4
        );
        $question
            ->setMultiselect(true)
            ->setErrorMessage('Random type %s is invalid.');

        $choices = $helper->ask($input, $output, $question);

        $type = [];
        foreach ($choices as $value) {
            $type[] = array_search($value, $types);
        }

        $str = StringUtils::random($length, array_sum($type));
        $output->writeln(sprintf('<comment>%s</comment>', $str));
    }
}