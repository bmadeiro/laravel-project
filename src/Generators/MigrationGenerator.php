<?php

namespace Bmadeiro\LaravelProject\Generators;

use Bmadeiro\LaravelProject\Parser\SchemaParser;
use Bmadeiro\LaravelProject\Syntax\AddForeignKeysToTable;
use Bmadeiro\LaravelProject\Syntax\AddToTable;
use Bmadeiro\LaravelProject\Syntax\RemoveForeignKeysFromTable;

class MigrationGenerator extends BaseGenerator implements GeneratorInterface
{
    public function __construct($command)
    {
        parent::__construct($command);
        $this->schemaParser = new SchemaParser();
    }

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
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'migration.stub';
    }

    /**
     * Get thelaravel default stub path for generate
     *
     * @return string
     */
    public function getLaravelDefaultTemplatePath()
    {
        return 'laravel\migration.stub';
    }

    public function generate($data = [])
    {
        $schema = $this->schemaParser->getFields($data['TABLE_NAME']);

        $migrationName = 'create_' . $data['TABLE_NAME'] . '_table';

        $filename = date('Y_m_d_His') . '_' . $migrationName . '.php';

        $data = array_merge([
            'CLASS' => ucwords(camel_case($migrationName)),
            'TABLE' => $data['TABLE_NAME'],
            'METHOD' => 'create',
        ], $data);

        if (empty($schema)) {
            if ($this->command->confirm('Table ' . $data['TABLE_NAME'] . ' don\'t exists. Do you wish to continue?')) {

                $templateData = $this->getLaravelDefaultTemplateData($data);

                $this->generateFile($filename, $templateData, $this->getLaravelDefaultTemplatePath());
            }
            else
                return;
        } else {

            $templateData = $this->getTemplateData($schema, $data);

            $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

            $this->generateFile($filename, $templateData, $templateName . '/' . $this->getTemplatePath());

            $schema = $this->schemaParser->getForeignKeyConstraints($data['TABLE_NAME']);

            if (empty($schema)) {
                return;
            }

            $this->command->info("\nSetting up Foreign Key Migrations for table " . $data['TABLE_NAME'] . "\n");

            $migrationName = 'add_foreign_keys_to_' . $data['TABLE_NAME'] . '_table';

            $filename = date('Y_m_d_His', strtotime('+1 second')) . '_' . $migrationName . '.php';

            //Change method type from $data array
            $data['METHOD'] = 'table';

            $templateData = $this->getTemplateData($schema, $data);

            $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

            $this->generateFile($filename, $templateData, $templateName . '/migration_ForeignKey.stub');
        }
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    public function getTemplateData($fields, $data = [])
    {
        if ($data['METHOD'] == 'create') {
            $up = (new AddToTable)->run($fields, $data['TABLE']);
            $down = '';
        } else {
            $up = (new AddForeignKeysToTable)->run($fields, $data['TABLE']);
            $down = (new RemoveForeignKeysFromTable)->run($fields, $data['TABLE']);
        }

        return array_merge($data, [
            'UP' => $up,
            'DOWN' => $down,
        ]);
    }

    /**
     * Fetch the stub data
     *
     * @return array
     */
    public function getLaravelDefaultTemplateData($data = [])
    {
        return array_merge($data, [
            'UP' => '',
            'DOWN' => '',
        ]);
    }
}
