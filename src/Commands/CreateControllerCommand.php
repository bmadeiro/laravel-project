<?php

namespace Bmadeiro\LaravelProject\Commands;

use Bmadeiro\LaravelProject\Generators\ControllerGenerator;
use Bmadeiro\LaravelProject\Generators\RequestGenerator;
use Bmadeiro\LaravelProject\Generators\RoutesGenerator;

class CreateControllerCommand extends CreateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:controller
                                {table?} : List table name for generate scaffold.}
                                {--tables= : a single table or a list of tables separated by a comma (,). No spaces.}
                                {--ignore= : List ignore table name.}
                                {--template= : Specify a custom template}
                                {--paginate=10 : Pagination for index.blade.php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller for given table.';
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
        return 'controller';
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
        //dd($messages);
        $configData = array_merge([
            'MESSAGE_STORE' => "'" . str_replace(':model', '$MODEL_NAME$', $messages['store']) . "'",
            'MESSAGE_UPDATE' => "'" . str_replace(':model', '$MODEL_NAME$', $messages['update']) . "'",
            'MESSAGE_DELETE' => "'" . str_replace(':model', '$MODEL_NAME$', $messages['delete']) . "'",
            'MESSAGE_NOT_FOUND' => "'" . str_replace(':model', '$MODEL_NAME$', $messages['not_found']) . "'",
        ], $configData);

        // init generators
        $routeGenerator = new RoutesGenerator($this);

        $requestGenerator = new RequestGenerator($this);

        $controllerGenerator = new ControllerGenerator($this);

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

            // create request files
            $requestGenerator->generate($data);

            // create a controller
            $controllerGenerator->generate($data);
        }
    }
}
