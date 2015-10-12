<?php

namespace Sloths\Application\Console\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
    /**
     * @return \Sloths\Application\Application
     */
    public function getSloths()
    {
        return $this->getApplication()->getSloths();
    }
}