<?php

namespace Sloths\Db\Migration;

use Sloths\Db\Database;
use Sloths\Misc\ArrayUtils;
use Sloths\Observer\ObserverTrait;

class Migrator
{
    use ObserverTrait;

    /**
     * @var Database
     */
    protected $database;

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
     * @param Database $database
     * @return $this
     */
    public function setDatabase(Database $database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @return Database
     * @throws \RuntimeException
     */
    public function getDatabase()
    {
        if (!$this->database) {
            throw new \RuntimeException('A database is required');
        }

        return $this->database;
    }

    /**
     * @return \Sloths\Db\Connection
     */
    public function getConnection()
    {
        return $this->getDatabase()->getWriteConnection();
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
     * @param $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
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
     * @return array
     */
    public function listMigrations()
    {
        $migrations = [];

        $dir = dir($this->directory);

        while (false !== ($f = $dir->read())) {
            if ('.' == $f || '..' == $f) {
                continue;
            }

            preg_match('/^(\d+)\-(.*)\.php$/', $f, $matches);
            list(, $version, $className) = $matches;

                $migrations[$version] = [
                'version'   => $version,
                'className' => $this->namespace . $className,
                'file'      => $this->directory . '/' . $f
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

    public function getLastMigrated()
    {
        $version = $this->getConnection()
            ->query("SELECT `version` FROM `{$this->table}` ORDER BY `version` DESC LIMIT 1")
            ->fetchColumn();

        if ($version) {
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
     * @return bool|array
     */
    public function rollback()
    {
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

        return $lastMigrated;
    }
}