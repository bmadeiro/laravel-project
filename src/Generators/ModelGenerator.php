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
    private $guardFields = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token'];

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

    /**
     * Get thelaravel default stub path for generate
     *
     * @return string
     */
    public function getLaravelDefaultTemplatePath()
    {
        return 'laravel\model.stub';
    }

    public function getTraitConfig()
    {
        $authImport = [
            'use Illuminate\Auth\Authenticatable;',
            'use Illuminate\Auth\Passwords\CanResetPassword;',
            'use Illuminate\Foundation\Auth\Access\Authorizable;',
            'use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;',
            'use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;',
            'use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;',
        ];

        $authTrait = ['Authenticatable', 'Authorizable', 'CanResetPassword'];

        return [
            'AUTH_IMPORT' => $authImport,

            'AUTH_TRAIT' => $authTrait,

            'AUTH_IMPLEMENTS' => ' implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract',

            'SOFT_DELETE_TRAIT' => 'SoftDeletes',

            'SOFT_DELETE_IMPORT' => "use Illuminate\Database\Eloquent\SoftDeletes;",
        ];
    }

    public function generate($data = [])
    {
        $this->isPivots = false;

        $schema = $this->schemaParser->getFields($data['TABLE_NAME']);

        $filename = $data['MODEL_NAME'].'.php';

        if (empty($schema)) {
            if ($this->command->confirm('Table ' . $data['TABLE_NAME'] . ' don\'t exists. Do you wish to continue?')) {
                $templateData = $this->getLaravelDefaultTemplateData($data);

                $this->generateFile($filename, $templateData, $this->getLaravelDefaultTemplatePath());
            }
            else
                return false;
        } else {
            $this->fillableColumns = $this->schemaParser->getFillableFieldsFromSchema($schema);

            $this->hiddenColumns = $this->schemaParser->getHiddenFieldsFromSchema($schema);

            $this->isPivots = count($this->schemaParser->checkPivots($data['TABLE_NAME'])) === 2 ? true : false;

            $templateData = $this->getTemplateData($schema, $data);

            if (!config('generator.pivot_scaffold', false) && $this->isPivots){
                return false;
            }

            $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

            $this->generateFile($filename, $templateData, $templateName . '/' . $this->getTemplatePath());
        }
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    public function getTemplateData($schema, $data = [])
    {
        $validations = $this->getValidationRules($data['TABLE_NAME']);

        if($validations === false)
            return false;

        $data['RULES'] = implode(",\n\t\t", $validations);

        $variables = $this->getTraitConfig();

        $importTraits = $traits = $fieldsHidden = [];
        if (isset($schema['deleted_at']) && $schema['deleted_at']['type'] === 'date') {
            $importTraits[] = $variables['SOFT_DELETE_IMPORT'];
            $traits[] = $variables['SOFT_DELETE_TRAIT'];
        }

        if ($this->command->type === 'model' && $this->command->option('auth')) {
            $importTraits = array_merge($importTraits, $variables['AUTH_IMPORT']);
            $traits = array_merge($traits, $variables['AUTH_TRAIT']);
            $data['AUTH_IMPLEMENTS'] = $variables['AUTH_IMPLEMENTS'];
        } else {
            $data['AUTH_IMPLEMENTS'] = '';
        }

        $data['IMPORT_TRAIT'] = !empty($importTraits) ? implode(PHP_EOL, $importTraits)."\n" : '';
        $data['USE_TRAIT'] = !empty($traits) ? "use ".implode(", ", $traits).";\n" : '';

        $data['PRIMARY_KEY'] = count($this->schemaParser->getPrimaryKey($data['TABLE_NAME'])) === 1 ? $this->schemaParser->getPrimaryKey($data['TABLE_NAME'])[0] : '';

        // generate fillable
        $fillableStr = [];
        foreach ($this->fillableColumns as $column) {
            $fillableStr[] = "'".$column['field']."'";
        }
        $data['FIELDS'] = implode(",\n\t\t", $fillableStr);

        ///generate hidden
        $hiddenFields = [];
        foreach ($this->hiddenColumns as $column) {
            $hiddenFields[] = "'".$column['field']."'";
        }

        $data['HIDDEN'] = implode(",\n\t\t", $hiddenFields);

        $data['CAST'] = implode(",\n\t\t", $this->getCasts());

        $functions = $this->relationshipGenerator->getFunctionsFromTable($data['TABLE_NAME']);
        $relationships = implode("\n", $functions);
        $data['RELATIONSHIPS'] = $relationships;

        return $data;
    }

    /**
     * Fetch the stub data
     *
     * @return array
     */
    public function getLaravelDefaultTemplateData($data = [])
    {
        $data['IMPORT_TRAIT'] = '';
        $data['USE_TRAIT'] = '';

        $data['AUTH_IMPLEMENTS'] = '';

        $data['FIELDS'] = "//";

        $data['HIDDEN'] = "//";

        $data['RULES'] = "//";

        $data['CAST'] = "//";

        $data['RELATIONSHIPS'] = "//";

        return $data;
    }

    private function getValidationRules($tableName)
    {
        $validations = [];
        $foreignKeys = $this->schemaParser->getForeignKeyConstraints($tableName);

        $existRules = [];
        foreach ($foreignKeys as $key) {
            if ((is_array($key['field']) ? count($key['field']) : 0) > 1) {
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
