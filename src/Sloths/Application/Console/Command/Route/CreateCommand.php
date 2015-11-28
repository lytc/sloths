<?php

namespace Sloths\Application\Console\Command\Route;

use Sloths\Application\Console\Command\Command;
use Sloths\Misc\Inflector;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
    protected $basicTemplate = <<<'EOT'
<?php
/* @var $this \Sloths\Application\Application */


$this->get('/', function(){
    /* @var $this \Sloths\Application\Application */

});

EOT;

    protected $restFulTemplate = <<<'EOT'
<?php

/* @var $this \Sloths\Application\Application */

$this->get('/', function(){
    /* @var $this \Sloths\Application\Application */

});

$this->get('/::id', function($id){
    /* @var $this \Sloths\Application\Application */

});

$this->get('/new', function(){
    /* @var $this \Sloths\Application\Application */

});

$this->post('/', function(){
    /* @var $this \Sloths\Application\Application */

});

$this->get('/::id/edit', function($id){
    /* @var $this \Sloths\Application\Application */

});

$this->put('/::id', function($id){
    /* @var $this \Sloths\Application\Application */

});

$this->delete('/::id', function($id){
    /* @var $this \Sloths\Application\Application */

});

EOT;

    protected $scaffoldTemplate = <<<'EOT'
<?php

/* @var $this \Sloths\Application\Application */
use Application\Model\{$modelName};

$this->get('/', function(){
    /* @var $this \Sloths\Application\Application */

    ${$name} = {$modelName}::all();

    return $this->render('{$path}/list', [
        '{$name}' => $this->paginator->paginate(${$name})
    ]);
});

$this->get('/::id', function($id){
    /* @var $this \Sloths\Application\Application */

    ${$nameSingular} = {$modelName}::first($id);
    ${$nameSingular} || $this->notFound();

    return $this->render('{$path}/view', [
        '{$nameSingular}' => ${$nameSingular}
    ]);
});

$this->get('/new', function(){
    /* @var $this \Sloths\Application\Application */

    return $this->render('{$path}/new', [

    ]);
});

$this->post('/', function(){
    /* @var $this \Sloths\Application\Application */
    $data = $this->params->only('');

    $validator = $this->validator->create([

    ]);

    if (!$validator->validate($data)) {
        return ['status' => 'error', 'formErrors' => $validator->getMessages()];
    }

    ${$nameSingular} = {$modelName}::create($data);
    ${$nameSingular}->save();

    $this->flashMessage->success(sprintf('{$nameTitleize} "%s" has been created', $this->view->e(${$nameSingular}->name)));
    return ['redirectTo' => $this->url->edit(${$nameSingular})];
});

$this->get('/::id/edit', function($id){
    /* @var $this \Sloths\Application\Application */

    ${$nameSingular} = {$modelName}::first($id);
    ${$nameSingular} || $this->notFound();

    return $this->render('{$path}/edit', [
        '{$nameSingular}' => ${$nameSingular}
    ]);
});

$this->put('/::id', function($id){
    /* @var $this \Sloths\Application\Application */

    ${$nameSingular} = {$modelName}::first($id);
    ${$nameSingular} || $this->notFound();

    $data = $this->params->only('');

    $validator = $this->validator->create([

    ]);

    if (!$validator->validate($data)) {
        return ['status' => 'error', 'formErrors' => $validator->getMessages()];
    }

    ${$nameSingular}->setData($data);
    ${$nameSingular}->save();

    return ['messages' => sprintf('{$nameTitleize} "%s" has been updated', $this->view->e(${$nameSingular}->name))];
});

$this->delete('/::id', function($id){
    /* @var $this \Sloths\Application\Application */

    ${$nameSingular} = {$modelName}::first($id);
    ${$nameSingular} || $this->notFound();

    ${$nameSingular}->delete();

    $this->flashMessage->success(sprintf('{$nameTitleize} "%s" has been removed', $this->view->e(${$nameSingular}->name)));
    $this->redirector->back();
});

EOT;

    protected function configure()
    {
        $this
            ->setName('route:create')
            ->setDescription('Create a new route')
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'The route path'
            )
            ->addOption('--restful', '-r', InputOption::VALUE_OPTIONAL, 'With RESTful', true)
            ->addOption('--scaffold', '-s', InputOption::VALUE_OPTIONAL, 'With Scaffold', true)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sloths = $this->getSloths();
        $routesPath = $sloths->getResourcePath('routes');
        $path = $input->getArgument('path');
        $file = $routesPath . '/' . $path . '.php';

        if (is_file($file)) {
            $output->writeln(sprintf('<error>Route file already exists in: %s</error>', $file));
        } else {
            $isRestFul = $input->getOption('restful');
            $isScaffold = $input->getOption('scaffold');

            $fileContent = $this->basicTemplate;

            if ($isScaffold) {
                $name = str_replace('/', '-', $path);
                $name = Inflector::camelize($name);
                $nameSingular = Inflector::singularize($name);
                $nameTitleize = Inflector::titleize($nameSingular);
                $modelName = ucfirst($nameSingular);

                $fileContent = str_replace(
                    ['{$path}', '{$name}', '{$nameSingular}', '{$nameTitleize}', '{$modelName}'],
                    [$path, $name, $nameSingular, $nameTitleize, $modelName],
                    $this->scaffoldTemplate
                );
            }elseif ($isRestFul) {
                $fileContent = $this->restFulTemplate;
            }

            $dir = pathinfo($file, PATHINFO_DIRNAME);

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            file_put_contents($file, $fileContent);
            $output->writeln(sprintf('Route file has been created at: <comment>%s</comment>', $file));

            if ($isScaffold) {
                $viewsPath = $sloths->getResourcePath('views');
                $viewsPath .= '/' . $path;

                if (!is_dir($viewsPath)) {
                    mkdir($viewsPath, 0777, true);
                }

                $viewExtension = $sloths->view->getExtension();
                foreach(['list', 'view', 'new', 'edit'] as $item) {
                    $file = $viewsPath . '/' . $item . $viewExtension;
                    file_put_contents($file, substr($file, strlen($sloths->getDirectory())));
                    $output->writeln(sprintf('View file has been created at: <comment>%s</comment>', $file));
                }
            }
        }
    }

}