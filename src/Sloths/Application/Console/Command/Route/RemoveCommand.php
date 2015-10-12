<?php

namespace Sloths\Application\Console\Command\Route;

use Sloths\Application\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class RemoveCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('route:remove')
            ->setDescription('Remove a route')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'The route path'
            )
            ->addOption('--scaffold', '-s', InputOption::VALUE_OPTIONAL, 'With Scaffold', true)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = [];

        $sloths = $this->getSloths();
        $routesPath = $sloths->getResourcePath('routes');
        $path = $input->getArgument('path');
        $file = $routesPath . '/' . $path . '.php';

        if (is_file($file)) {
            $files[] = $file;
        }

        $isScaffold = $input->getOption('scaffold');

        if ($isScaffold) {
            $viewsPath = $sloths->getResourcePath('views');
            $viewsPath .= '/' . $path;

            $viewExtension = $sloths->view->getExtension();
            foreach(['list', 'view', 'new', 'edit'] as $item) {
                $file = $viewsPath . '/' . $item . $viewExtension;
                if (is_file($file)) {
                    $files[] = $file;
                }
            }
        }

        if (!$files) {
            $output->writeln('<info>Nothing to remove!</info>');
        } else {
            $output->writeln(count($files) . ' file(s) below will be remove:');

            foreach ($files as $file) {
                $output->writeln(sprintf('<comment>%s</comment>', $file));
            }

            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Are you sure?<comment>[yes/no]</comment>: ', false, '/^yes$/i');

            if (!$helper->ask($input, $output, $question)) {
                return;
            }

            foreach ($files as $file) {
                unlink($file);
                $output->writeln(sprintf('File has been removed: <comment>%s</comment>', $file));
            }

            # remove view folder if it's empty
            if ($isScaffold && is_dir($viewsPath) && 2 === count(scandir($viewsPath))) {
                rmdir($viewsPath);
                $output->writeln(sprintf('Folder has been removed: <comment>%s</comment>', $viewsPath));
            }
        }
    }

}