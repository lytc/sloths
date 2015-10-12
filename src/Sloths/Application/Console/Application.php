<?php

namespace Sloths\Application\Console;

use Sloths\Application\ApplicationInterface;
use Symfony\Component\Console\Application as SymfonyConsoleApplication;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Sloths\Application\Console\Command;

class Application extends SymfonyConsoleApplication
{
    /**
     * @var ApplicationInterface
     */
    protected $sloths;

    /**
     * @param ApplicationInterface $sloths
     */
    public function __construct(ApplicationInterface $sloths)
    {
        parent::__construct('SLOTHS FRAMEWORK', $sloths::VERSION);

        $this->sloths = $sloths;
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(ConsoleEvents::COMMAND, function(ConsoleCommandEvent $event) use ($sloths) {
            $input = $event->getInput();
            $resourceDirectory = $input->getParameterOption(['--resource']);

            if ($resourceDirectory) {
                $sloths->setResourceDirectory($resourceDirectory);
            }
        });

        $this->setDispatcher($dispatcher);

    }

    /**
     * @return array
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new Command\EnvCommand(),
            new Command\PasswordCommand(),
            new Command\StrRandCommand(),
            new Command\ServiceListCommand(),

            new Command\Route\ListCommand(),
            new Command\Route\CreateCommand(),
            new Command\Route\RemoveCommand(),

            new Command\Db\CreateCommand(),
            new Command\Db\DropCommand(),
            new Command\Db\SeedCommand(),
            new Command\Db\SetupCommand(),

            new Command\Migration\SetupCommand(),
            new Command\Migration\VersionCommand(),
            new Command\Migration\CreateCommand(),
            new Command\Migration\ListCommand(),
            new Command\Migration\MigrateCommand(),
            new Command\Migration\RollbackCommand(),
            new Command\Migration\ResetCommand(),

            new Command\Model\SyncCommand()
        ]);
    }

    public function getSloths()
    {
        $this->sloths->boot();
        return $this->sloths;
    }

    protected function getDefaultInputDefinition()
    {
        $inputDefinition = parent::getDefaultInputDefinition();
        $inputDefinition->addOption(new InputOption('--resource', null, InputOption::VALUE_OPTIONAL, 'The resource directory'));
        return $inputDefinition;
    }


}