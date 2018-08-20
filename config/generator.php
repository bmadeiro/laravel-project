<?php

return [

    'custom_template' => false,

    /*
    |--------------------------------------------------------------------------
    | Crud Generator Template Stubs Storage Path
    |--------------------------------------------------------------------------
    |
    | Here you can specify your custom template path for the generator.
    |
     */

    'path' => base_path('resources/generator/'),

    

    /*
    |--------------------------------------------------------------------------
    | Path for classes
    |--------------------------------------------------------------------------
    |
    | All Classes will be created on these relevant path
    |
     */

    'path_migration'  => base_path('database/migrations/'),
    
    'path_model'      => app_path('Models/'),
    
    'path_repository' => app_path('Repositories/'),
    
    'path_service'    => app_path('Services/'),
    
    'path_controller' => app_path('Controllers/'),
    
    'path_view'       => base_path('resources/views/'),
    
    'path_request'    => app_path('Requests/'),
    
    'path_route'      => app_path('routes.php'),
    
    'path_factory'    => base_path('database/factories/ModelFactory.php'),

    /*
    |--------------------------------------------------------------------------
    | Namespace for classes
    |--------------------------------------------------------------------------
    |
    | All Classes will be created with these namespaces
    |
     */

    'namespace_model'      => 'Dais\Models',
    
    'namespace_repository' => 'Dais\Repositories',
    
    'namespace_service'    => 'Dais\Services',
    
    'namespace_controller' => 'Dais\Controllers',
    
    'namespace_request'    => 'Dais\Requests',

    /**
     * Columns number to show in view's table.
     */
    'view_columns_number' => 3,

    /**
     * Delimiter for template vars
     */
    'custom_delimiter' => ['%%', '%%'],

    /*
    |--------------------------------------------------------------------------
    | Dynamic templating
    |--------------------------------------------------------------------------
    |
    | Here you can specify your customs templates for the generator.
    | You can set new templates or delete some templates if you do not want them.
    | You can also choose which values are passed to the views and you can specify a custom delimiter for all templates
    |
    | Those values are available :
    |
    | formFields
    | formFieldsHtml
    | varName
    | crudName
    | crudNameCap
    | crudNameSingular
    | primaryKey
    | modelName
    | modelNameCap
    | viewName
    | routePrefix
    | routePrefixCap
    | routeGroup
    | formHeadingHtml
    | formBodyHtml
    |
    |
     */
    'dynamic_view_template' => [
        'index' => ['formHeadingHtml', 'formBodyHtml', 'crudName', 'crudNameCap', 'modelName', 'viewName', 'routeGroup', 'primaryKey'],
        'form' => ['formFieldsHtml'],
        'create' => ['crudName', 'crudNameCap', 'modelName', 'modelNameCap', 'viewName', 'routeGroup', 'viewTemplateDir'],
        'edit' => ['crudName', 'crudNameSingular', 'crudNameCap', 'modelNameCap', 'modelName', 'viewName', 'routeGroup', 'primaryKey', 'viewTemplateDir'],
        'show' => ['formHeadingHtml', 'formBodyHtml', 'formBodyHtmlForShowView', 'crudName', 'crudNameSingular', 'crudNameCap', 'modelName', 'viewName', 'routeGroup', 'primaryKey'],
        /*
         * Add new stubs templates here if you need to, like action, datatable...
         * custom_template needs to be activated for this to work
         */
    ]


];
