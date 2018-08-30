<?php

namespace Peaches\Generator\Generators;

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

    public function generate($data = [])
    {
        $contexts = ['Create', 'Update'];

        $templateData = $this->getExtendsClass('request', $data);

        foreach ($contexts as $context) {
            $templateData['REQUEST_CONTEXT'] = $context;
            $filename =  $context . $data['MODEL_NAME'] . 'Request.php';

            $this->generateFile($filename, $templateData);
        }
    }
}
