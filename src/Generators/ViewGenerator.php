<?php

namespace Bmadeiro\LaravelProject\Generators;

use Bmadeiro\LaravelProject\Parser\SchemaParser;

class ViewGenerator extends BaseGenerator implements GeneratorInterface
{
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
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'views/';
    }

    /**
     * Get thelaravel default stub path for generate
     *
     * @return string
     */
    public function getLaravelDefaultTemplatePath()
    {
        return 'laravel/views/';
    }

    public function getPaginatePath()
    {
        return $this->templatePath . 'paginate.blade.stub';
    }

    public function generate($data = [])
    {
        $this->schemaParser = new SchemaParser();

        $schema = $this->schemaParser->getFields($data['TABLE_NAME']);

        $this->fillableColumns = $this->schemaParser->getFillableFieldsFromSchema($schema);

        // set path to view folder
        $this->rootPath = config('generator.path_view') . $data['VIEW_FOLDER_NAME'] . '/';

        $this->command->comment("\nViews created: ");
        $this->templateData = $data;

        $this->generateIndex();
        $this->generateForm();
        $this->generateCreate();
        $this->generateEdit();
        $this->generateShow();
    }

    private function generateIndex()
    {
        $templateData = $this->templateData;

        if ($this->command->option('paginate')) {
            $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

            $templateData['PAGINATE'] = $this->generateContent($templateName . '/' . $this->getPaginatePath(), $templateData);
        } else {
            $templateData['PAGINATE'] = '';
        }

        $headerColumns = $bodyColumns = [];
        foreach ($this->fillableColumns as $column) {
            $headerColumns[] = '<th>' . title_case(str_replace('_', ' ', $column['field'])) . "</th>";

            $bodyColumns[] = '<td>{!! $' . $templateData['MODEL_NAME_CAMEL'] . '->' . $column['field'] . " !!}</td>";
        }

        $templateData['FIELD_HEADER'] = implode("\n\t\t\t\t", $headerColumns);
        $templateData['FIELD_BODY'] = implode("\n\t\t\t\t\t", $bodyColumns);

        $filename = 'index.blade.php';

        $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

        $this->generateFile($filename, $templateData, $templateName . '/' . $this->templatePath . 'index.blade.stub');
    }

    private function generateForm()
    {
        $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

        $fieldTemplate = $this->getTemplate($templateName . '/' . $this->templatePath . 'form_field.blade.stub');

        $fields = [];
        logger($this->fillableColumns);
        foreach ($this->fillableColumns as $column) {
            switch ($column['type']) {
                case 'integer':
                    $inputType = 'number';
                    break;
                case 'text':
                    $inputType = 'textarea';
                    break;
                case 'date':
                    $inputType = $column['type'];
                    break;
                case 'boolean':
                    $inputType = 'checkbox';
                    break;
                default:
                    $inputType = 'text';
                    break;
            }

            $fields[] = $this->compile($fieldTemplate, [
                'FIELD_NAME' => $column['field'],
                'LABEL' => title_case(str_replace('_', ' ', $column['field'])),
                'INPUT_TYPE' => $inputType,
            ]);
        }

        $templateData = $this->templateData;
        $templateData['FIELDS'] = implode("\n\n", $fields);

        $filename = 'form.blade.php';

        $this->generateFile($filename, $templateData, $templateName . '/' . $this->templatePath . 'form.blade.stub');
    }

    private function generateShow()
    {
        $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

        $fieldTemplate = $this->getTemplate($templateName . '/' . $this->templatePath . 'form_field.blade.stub');

        $fields = [];
        foreach ($this->fillableColumns as $column) {
            $fields[] = $this->compile($fieldTemplate, [
                'FIELD_NAME' => $column['field'],
                'LABEL' => title_case(str_replace('_', ' ', $column['field'])),
            ]);
        }

        $templateData = $this->templateData;
        $templateData['FIELDS'] = implode("\n\n", $fields);

        $filename = 'show.blade.php';

        $this->generateFile($filename, $templateData, $templateName . '/' . $this->templatePath . 'show.blade.stub');
    }

    private function generateCreate()
    {
        $filename = 'create.blade.php';

        $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

        $this->generateFile($filename, $this->templateData, $templateName . '/' . $this->templatePath . 'create.blade.stub');
    }

    private function generateEdit()
    {
        $filename = 'edit.blade.php';

        $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

        $this->generateFile($filename, $this->templateData, $templateName . '/' . $this->templatePath . 'edit.blade.stub');
    }
}
