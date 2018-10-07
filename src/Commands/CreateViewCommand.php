<?php

namespace Bmadeiro\LaravelProject\Commands;

use Bmadeiro\LaravelProject\Generators\ModelGenerator;
use Bmadeiro\LaravelProject\Generators\ViewGenerator;

class CreateViewCommand extends CreateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:view
                                {table?} : List table name for generate view.}
                                {--tables= : a single table or a list of tables separated by a comma (,). No spaces.}
                                {--ignore= : List ignore table name.}
                                {--template= : Specify a custom template}
                                {--paginate=10 : Pagination for index.blade.php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a view for given table.';
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
        return 'view';
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

        // init generators
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

            // create a view folder
            $viewGenerator->generate($data);
        }
    }
}
