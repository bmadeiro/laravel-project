<?php

namespace Bmadeiro\LaravelProject\Commands;

use Bmadeiro\LaravelProject\Generators\ModelGenerator;

class CreateModelCommand extends CreateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:model
                            {table?} : Table name for generate model file}
                            {--tables= : a single table or a list of tables separated by a comma (,)}
                            {--ignore= : List ignore table name}
                            {--soft-deletes=no : Include soft deletes fields}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model.';
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
        return 'model';
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        if ($this->option('models')) {
            $this->models = explode(',', $this->option('models'));
        }

        // TODO: compare the length option

        $this->comment('Generating models for: ' . implode(',', $this->tables));

        $configData = $this->getConfigData();

        $modelGenerator = new ModelGenerator($this);

        foreach ($this->tables as $idx => $tableName) {
            if (isset($this->models[$idx])) {
                $modelName = $this->models[$idx];
            } else {
                $modelName = str_singular(studly_case($tableName));
            }

            $data = array_merge([
                'TABLE_NAME' => $tableName,
                'MODEL_NAME' => $modelName,
            ], $configData);

            $modelGenerator->generate($data);
        }
    }
}
