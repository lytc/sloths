<?php

namespace Sloths\Application\Console\Command\Migration;

use Sloths\Application\Console\Command\Command;
use Sloths\Misc\Inflector;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
    protected $template = <<<'EOT'
<?php

namespace {$namespace};
use \Sloths\Db\Migration\AbstractMigration;

class {$className} extends AbstractMigration
{
    public function up()
    {

    }

    public function down()
    {

    }
}

EOT;

    protected function configure()
    {
        $this
            ->setName('migration:create')
            ->setDescription('Create a migration class')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The migration name'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = $this->getSloths()->migrator;
        $directory = $migrator->getDirectory();
        $namespace = $migrator->getNamespace();

        $name = $input->getArgument('name');
        $className = Inflector::classify($name);
        $version = gmdate('YmdHis');
        $file = $directory . '/' . $version . '-' . $className . '.php';
        $fileContent = str_replace(['{$namespace}', '{$className}'], [$namespace, $className], $this->template);
        file_put_contents($file, $fileContent);

        $output->writeln(sprintf('Migration file has been created at: <comment>%s</comment>', $file));
    }
}