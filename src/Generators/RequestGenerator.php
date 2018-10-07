<?php

namespace Bmadeiro\LaravelProject\Generators;

class RequestGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'request';
    }

    /**
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'request.stub';
    }

    /**
     * Get thelaravel default stub path for generate
     *
     * @return string
     */
    public function getLaravelDefaultTemplatePath()
    {
        return 'laravel\request.stub';
    }

    public function generate($data = [])
    {
        $contexts = ['Create', 'Update'];

        $templateData = $this->getExtendsClass('request', $data);

        foreach ($contexts as $context) {
            $templateData['REQUEST_CONTEXT'] = $context;
            $filename =  $context . $data['MODEL_NAME'] . 'Request.php';

            $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

            $this->generateFile($filename, $templateData, $templateName . '/' . $this->getTemplatePath());
        }
    }
}
