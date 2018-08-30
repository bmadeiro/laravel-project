<?php

namespace Bmadeiro\LaravelProject\Commands;

use Bmadeiro\LaravelProject\Generators\ControllerGenerator;
use Bmadeiro\LaravelProject\Generators\ModelGenerator;
use Bmadeiro\LaravelProject\Generators\RequestGenerator;
use Bmadeiro\LaravelProject\Generators\RoutesGenerator;
use Bmadeiro\LaravelProject\Generators\ViewGenerator;

class CreateProjectCommand extends CreateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:project
                                {table?} : List table name for generate scaffold.}
                                {--tables= : List table name for generate scaffold.}
                                {--ignore= : List ignore table name.}
                                {--paginate=10 : Pagination for index.blade.php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full CRUD for given table with initial views.';
    /**
     * A list model name for generate
     *
     * @var array
     */
    public $models = [];

    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'scaffold';
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        // TODO: compare the length option

        $configData = $this->getConfigData();

        // get message config by locale
        $locale = config('app.locale');
        $configMessages = config('generator.message');
        if (isset($configMessages[$locale])) {
            $messages = $configMessages[$locale];
        } else {
            $messages = $configMessages['en'];
        }
        $configData = array_merge([
            'MESSAGE_STORE' => "'" . str_replace(':model', '$MODEL_NAME$', $messages['store']) . "'",
            'MESSAGE_UPDATE' => "'" . str_replace(':model', '$MODEL_NAME$', $messages['update']) . "'",
            'MESSAGE_DELETE' => "'" . str_replace(':model', '$MODEL_NAME$', $messages['delete']) . "'",
            'MESSAGE_NOT_FOUND' => "'" . str_replace(':model', '$MODEL_NAME$', $messages['not_found']) . "'",
        ], $configData);

        // init generators
        $routeGenerator = new RoutesGenerator($this);
        $modelGenerator = new ModelGenerator($this);

        $requestGenerator = new RequestGenerator($this);
        $controllerGenerator = new ControllerGenerator($this);
        $viewGenerator = new ViewGenerator($this);

        // generate files for every table
        foreach ($this->tables as $idx => $tableName) {
            if (isset($this->models[$idx])) {
                $modelName = $this->models[$idx];
            } else {
                $modelName = str_singular(studly_case($tableName));
            }

            $this->comment('Generating controller for: ' . $tableName);

            $data = array_merge($configData, [
                'TABLE_NAME' => $tableName,
                'MODEL_NAME' => $modelName,
                'MODEL_NAME_CAMEL' => camel_case($modelName),
                'MODEL_NAME_PLURAL' => str_plural($modelName),
                'MODEL_NAME_PLURAL_CAMEL' => camel_case(str_plural($modelName)),
                'RESOURCE_URL' => str_slug($tableName),
                'VIEW_FOLDER_NAME' => snake_case($tableName),
            ]);

            // update route
            $routeGenerator->generate($data);

            // create a model
            $modelGenerator->generate($data);

            // create request files
            $requestGenerator->generate($data);

            // create a controller
            $controllerGenerator->generate($data);

            // create a view folder
            $viewGenerator->fillableColumns = $modelGenerator->fillableColumns;
            $viewGenerator->generate($data);
        }
    }
}
