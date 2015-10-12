<?php

namespace Sloths\Application\Service;

use DebugBar\StandardDebugBar;

class DebugBar extends StandardDebugBar implements ServiceInterface
{
    use ServiceTrait;

    public function boot()
    {
        $pdoCollector = new \DebugBar\DataCollector\PDO\PDOCollector();

        $database = $this->getApplication()->database;
        $pdoRead = $database->getReadConnection()->getPdo();
        $pdoWrite = $database->getWriteConnection()->getPdo();

        $traceablePdoRead  = new \DebugBar\DataCollector\PDO\TraceablePDO($pdoRead);
        $traceablePdoWrite = new \DebugBar\DataCollector\PDO\TraceablePDO($pdoWrite);
        $database->getReadConnection()->setPdo($traceablePdoRead);

        if ($pdoRead != $pdoWrite) {
            $database->getWriteConnection()->setPdo($traceablePdoWrite);
            $pdoCollector->addConnection($traceablePdoRead, 'read-db');
            $pdoCollector->addConnection($traceablePdoWrite, 'write-db');
        } else {
            $pdoCollector->addConnection($traceablePdoRead, '');
        }

        $this->addCollector($pdoCollector);
    }
}