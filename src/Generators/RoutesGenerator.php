<?php

namespace Bmadeiro\LaravelProject\Generators;

class RoutesGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'route';
    }

    /**
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'route.stub';
    }

    /**
     * Get thelaravel default stub path for generate
     *
     * @return string
     */
    public function getLaravelDefaultTemplatePath()
    {
        return 'laravel\route.stub';
    }

    public function generate($data = [])
    {
        $data['RESOURCE_URL'] = str_slug($data['TABLE_NAME']);

        $templateName = ($this->command->option('template') ? $this->command->option('template') : config("generator.template"));

        $routeContent = "\n\n" . $this->generateContent($templateName . '/' . $this->getTemplatePath(), $data);

        $this->command->info("\nUpdate route for resources:" . $data['TABLE_NAME']);

        $this->fileHelper->append($this->rootPath, $routeContent);
    }
}
