<?php

namespace Bmadeiro\LaravelProject\Generators;

use Bmadeiro\LaravelProject\Parser\SchemaParser;

class ModelGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * A list guard columns
     *
     * @var array
     */
    private $guardFields = ['created_at', 'updated_at', 'deleted_at', 'remember_token'];

    public $isPivots;

    public function __construct($command)
    {
        parent::__construct($command);
        $this->schemaParser = new SchemaParser();
        $this->relationshipGenerator = new ModelRelationshipsGenerator();
    }

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
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'model.stub';
    }

    public function getTraitConfig()
    {
        return [
            'SOFT_DELETE_TRAIT' => 'SoftDeletes',

            'SOFT_DELETE_IMPORT' => "use Illuminate\Database\Eloquent\SoftDeletes;",
        ];
    }

    public function generate($data = [])
    {
        $this->isPivots = false;
        $schema = $this->schemaParser->getFields($data['TABLE_NAME']);

        if (empty($schema)) {
            return false;
        }

        $this->fillableColumns = $this->schemaParser->getFillableFieldsFromSchema($schema);

        $this->isPivots = count($this->schemaParser->checkPivots($data['TABLE_NAME'])) === 2 ? true : false;

        $filename = $data['MODEL_NAME'].'.php';

        $templateData = $this->getTemplateData($schema, $data);

        $templateData = $this->getExtendsClass('model', $templateData);

        if (!config('generator.pivot_scaffold', false) && $this->isPivots) {
            return false;
        }

        $this->generateFile($filename, $templateData);
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    public function getTemplateData($schema, $data = [])
    {
        $validations = $this->getValidationRules($data['TABLE_NAME']);

        if ($validations === false) {
            return false;
        }

        $data['RULES'] = implode(",\n\t\t", $validations);

        $importTraits = $traits = [];
        if (isset($schema['deleted_at']) && $schema['deleted_at']['type'] === 'date') {
            $importTraits[] = $variables['SOFT_DELETE_IMPORT'];
            $traits[] = $variables['SOFT_DELETE_TRAIT'];
        }

        $data['IMPORT_TRAIT'] = !empty($importTraits) ? implode(PHP_EOL, $importTraits)."\n" : '';
        $data['USE_TRAIT'] = !empty($traits) ? "use ".implode(", ", $traits).";\n" : '';

        // generate fillable
        $fillableStr = [];
        foreach ($this->fillableColumns as $column) {
            $fillableStr[] = "'".$column['field']."'";
        }
        $data['FIELDS'] = implode(",\n\t\t", $fillableStr);

        $data['CAST'] = implode(",\n\t\t", $this->getCasts());

        $functions = $this->relationshipGenerator->getFunctionsFromTable($data['TABLE_NAME']);
        $relationships = implode("\n", $functions);
        $data['RELATIONSHIPS'] = $relationships;

        return $data;
    }

    private function getValidationRules($tableName)
    {
        $validations = [];
        $foreignKeys = $this->schemaParser->getForeignKeyConstraints($tableName);

        $existRules = [];
        foreach ($foreignKeys as $key) {
            if (count($key['field']) > 1) {
                continue;
            }

            $existRules[$key['field']] = 'exists:'.$key['on'].','.$key['references'];
        }

        foreach ($this->fillableColumns as $column) {
            $rules = [];

            if (!isset($column['decorators']) || !in_array('nullable', $column['decorators'])) {
                $rules[] = 'required';
            }

            switch ($column['type']) {
                case 'integer':
                case 'smallInteger':
                case 'bigInteger':
                    $rules[] = 'integer';
                    break;
                case 'string':
                    $rules[] = 'string';
                    if (isset($column['args']) && !empty($column['args'])) {
                        $rules[] = 'max:' . $column['args'];
                    }
                    break;
                case 'email':
                case 'password':
                case 'date':
                case 'boolean':
                    $rules[] = $column['type'];
                    break;
            }

            if (isset($existRules[$column['field']])) {
                $rules[] = $existRules[$column['field']];
            }

            if (!empty($rules)) {
                $validations[] = "'".$column['field']."' => '".implode('|', $rules)."'";
            }
        }

        return $validations;
    }

    public function getCasts()
    {
        $casts = [];

        foreach ($this->fillableColumns as $column) {
            switch ($column['type']) {
                case 'integer':
                case 'smallInteger':
                case 'bigInteger':
                    $inputType = 'integer';
                    break;
                case 'double':
                case 'float':
                case 'boolean':
                case 'date':
                    $inputType = $column['type'];
                    break;
                case 'string':
                case 'char':
                case 'text':
                    $inputType = 'string';
                    break;
                default:
                    $inputType = '';
                    break;
            }

            if (!empty($inputType)) {
                $casts[] = "'".$column['field']."' => '".$inputType."'";
            }
        }

        return $casts;
    }
}
