<?php

namespace Bmadeiro\LaravelProject\Commands;

use Bmadeiro\LaravelProject\Generators\MigrationGenerator;

class CreateMigrationCommand extends CreateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:migration
                                {table?} : List table name for generate migration files.}
                                {--tables= : a single table or a list of tables separated by a comma (,). No spaces.}
                                {--ignore= : List ignore table name.}
                                {--template= : Specify a custom template}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migrate from exist tables';

    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'migration';
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $this->comment('Generating migrations for: ' . implode(', ', $this->tables));

        $migrationGenerator = new MigrationGenerator($this);

        foreach ($this->tables as $tableName) {
            $data = [
                'TABLE_NAME' => $tableName,
            ];

            $migrationGenerator->generate($data);
        }
    }
}
