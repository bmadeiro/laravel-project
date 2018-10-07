<?php

namespace Bmadeiro\LaravelProject\Generators;

class ControllerGenerator extends BaseGenerator implements GeneratorInterface
{
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
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'controller.stub';
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

    public function generate($data = [])
    {
        if ($this->command->option('paginate')) {
            $data['RENDER_TYPE'] = 'paginate(' . $this->command->option('paginate') . ')';
        } else {
            $data['RENDER_TYPE'] = 'all()';
        }

        $filename = $data['MODEL_NAME'] . 'Controller.php';

        $templateData = $this->getExtendsClass('controller',$data);

        $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

        $this->generateFile($filename, $templateData, $templateName . '/' . $this->getTemplatePath());
    }

    public function requestLayer($configData, $modelName, $useRequestLayer = false)
    {
        if ($useRequestLayer) {
            $requestData = [
                'USE_REQUEST' => "use {$configData['NAMESPACE_REQUEST']}\\Create{$modelName}Request;\nuse {$configData['NAMESPACE_REQUEST']}\\Update{$modelName}Request;",
                'UPDATE_REQUEST' => "Update{$modelName}Request",
                'CREATE_REQUEST' => "Create{$modelName}Request"
            ];
        }
        else
        {
            $requestData = [
                'USE_REQUEST' => "use {$configData['NAMESPACE_REQUEST']} . ';'",
                'UPDATE_REQUEST' => 'Request',
                'CREATE_REQUEST' => 'Request'
            ];
        }

        return  $requestData;
    }
}
