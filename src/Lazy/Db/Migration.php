<?php

namespace Lazy\Db;
use Lazy\Db\Connection;

class Migration
{
    protected $directory;

    /**
     * @var Connection
     */
    protected $connection;

    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    public function run()
    {
        // list all migrations
        $dir = dir($this->directory);
        $migrations = [];

        while (false !== ($f = $dir->read())) {
            if ('.' == $f || '..' == $f) {
                continue;
            }

            $parts = explode('-', $f);

            $migrations[$parts[0]] = [
                'className' => pathinfo($parts[1], PATHINFO_FILENAME),
                'file'      => $f
            ];
        }

        $dir->close();

        ksort($migrations);

        if (!$migrations) {
            echo 'No migration to run!';
            return;
        }

        $migrationRuns = [];

        $this->connection->beginTransaction();

        try {
            foreach ($migrations as $version => $info) {
                # check if is already run
                $stmt = $this->connection->query("SELECT version FROM migrations WHERE version='{$version}' LIMIT 1");
                $row = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($row) {
                    continue;
                }

                require_once $this->directory . '/' . $info['file'];
                $migrationClass = new $info['className']($this->connection);
                $migrationClass->up();
                $migrationRuns[] = $migrationClass;

                $this->connection->exec("INSERT INTO migrations(version) VALUES ('{$version}')");

                echo sprintf("-- %s\n", $info['file']);
            }
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();

            foreach ($migrationRuns as $migrationClass) {
                $migrationClass->down();
            }

            throw $e;
        }

        if (!$migrationRuns) {
            echo 'No migration to run!';
        }
    }
}