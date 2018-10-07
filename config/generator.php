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

    'path_controller' => app_path('Http/Controllers/'),

    'path_view'       => base_path('resources/views/'),

    'path_request'    => app_path('Requests/'),

    'path_route'      => base_path('routes/web.php'),

    /*
    |--------------------------------------------------------------------------
    | Namespace for classes
    |--------------------------------------------------------------------------
    |
    | All Classes will be created with these namespaces
    |
     */

    'namespace_model'      => 'App\Models',

    'namespace_controller' => 'App\Http\Controllers',

    'namespace_request'    => 'App\Requests',

    /*
    |--------------------------------------------------------------------------
    | Message
    |--------------------------------------------------------------------------
     */
    'message' => [
        'en'  => [
            'store'     => ':model saved successfully.',
            'update'    => ':model updated successfully.',
            'delete'    => ':model deleted successfully.',
            'not_found' => ':model not found',
        ],
    ],
];
