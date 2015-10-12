<?php

namespace Sloths\Db\Migration;

use Sloths\Db\ConnectionManager;
use Sloths\Misc\ArrayUtils;
use Sloths\Observer\ObserverTrait;

class Migrator
{
    use ObserverTrait;

    /**
     * @var ConnectionManager
     */
    protected $connectionManager;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $table = 'migrations';

    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @param ConnectionManager $connectionManager
     * @return $this
     */
    public function setConnectionManager(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
        return $this;
    }

    /**
     * @return ConnectionManager
     * @throws \RuntimeException
     */
    public function getConnectionManager()
    {
        if (!$this->connectionManager) {
            throw new \RuntimeException('A database connection manager is required');
        }

        return $this->connectionManager;
    }

    /**
     * @return \Sloths\Db\Connection
     */
    public function getConnection()
    {
        return $this->getConnectionManager()->getWriteConnection();
    }

    /**
     * @param $directory
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException('Invalid directory: ' . $directory);
        }

        $this->directory = realpath($directory);
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function listMigrations()
    {
        $migrations = [];
        $migratedVersions = $this->listMigratedVersion();
        $dir = dir($this->directory);

        while (false !== ($f = $dir->read())) {
            if ('.' == $f || '..' == $f) {
                continue;
            }

            preg_match('/^(\d+)\-(.*)\.php$/', $f, $matches);
            list(, $version, $className) = $matches;

            $migrations[$version] = [
                'version'   => $version,
                'className' => $this->namespace . '\\' . $className,
                'file'      => $this->directory . '/' . $f,
                'migrated'  => in_array($version, $migratedVersions)
            ];
        }

        $dir->close();

        ksort($migrations);
        return $migrations;
    }

    /**
     * @return array
     */
    public function listMigratedVersion()
    {
        $migratedVersions = $this->getConnection()
            ->query("SELECT `version` FROM `{$this->table}`")
            ->fetchAll(\PDO::FETCH_COLUMN, 0)
        ;

        return $migratedVersions;
    }

    /**
     * @return int
     */
    public function getMigratedCount()
    {
        return count($this->listMigratedVersion());
    }

    /**
     * @return array
     */
    public function listMigrated()
    {
        return ArrayUtils::only($this->listMigrations(), $this->listMigratedVersion());
    }

    /**
     * @return array
     */
    public function listPending()
    {
        return ArrayUtils::except($this->listMigrations(), $this->listMigratedVersion());
    }

    /**
     * @return string
     */
    public function getLastMigratedVersion()
    {
        $version = $this->getConnection()
            ->query("SELECT `version` FROM `{$this->table}` ORDER BY `version` DESC LIMIT 1")
            ->fetchColumn();

        return $version;
    }

    /**
     * @return \Sloths\Db\Migration\MigrationInterface
     */
    public function getLastMigrated()
    {
        if ($version = $this->getLastMigratedVersion()) {
            $migrations = $this->listMigrations();
            return $migrations[$version];
        }

        return false;
    }

    /**
     * @return array
     */
    public function migrate()
    {
        $migrations = $this->listPending();

        foreach ($migrations as $version => $migration) {
            $this->triggerEventListener('migrate', [$migration]);

            require_once $migration['file'];
            $migrationClass = new $migration['className'];
            $migrationClass->setConnection($this->getConnection());
            $migrationClass->up();

            $this->getConnection()->exec("INSERT INTO `{$this->table}` SET `version` = '{$version}'");

            $this->triggerEventListener('migrated', [$migration]);
        }

        return $migrations;
    }

    /**
     * - 1 will rollback all migrations
     * @param int $steps
     * @return bool|array
     */
    public function rollback($steps = 1)
    {
        if ($steps == -1) {
            $steps = $this->getMigratedCount();
        }

        for ($i = 0; $i < $steps; $i++) {
            $lastMigrated = $this->getLastMigrated();

            if ($lastMigrated) {
                $this->triggerEventListener('rollback', [$lastMigrated]);

                require_once $lastMigrated['file'];
                $migrationClass = new $lastMigrated['className'];

                $migrationClass->setConnection($this->getConnection());
                $migrationClass->down();

                $version = $lastMigrated['version'];
                $this->getConnection()->exec("DELETE FROM `{$this->table}` WHERE `version` = '{$version}'");

                $this->triggerEventListener('rolledback', [$lastMigrated]);
            }
        }

        return $lastMigrated;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->rollback(-1);
        $this->migrate();

        return $this;
    }
}