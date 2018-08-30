<?php

class LaravelProjectTest extends TestCase
{
    public function testLaravelProjectCommand()
    {
        // $this->artisan('create:crud', [
        //     'name' => 'Posts'
        // ]);
        // $this->assertContains('Controller already exists!', $this->consoleOutput());
    }

    public function testCreateControllerCommand()
    {
        $this->artisan('create:controller', [
            'name' => 'CustomersController',
            '--crud-name' => 'customers',
            '--model-name' => 'Customer',
        ]);

        $this->assertContains('Controller created successfully.', $this->consoleOutput());

        $this->assertFileExists(app_path('Http/Controllers') . '/CustomersController.php');
    }

    public function testCreateModelCommand()
    {
        $this->artisan('create:model', [
            'name' => 'Customer',
            '--fillable' => "['name', 'email']",
        ]);

        $this->assertContains('Model created successfully.', $this->consoleOutput());

        $this->assertFileExists(app_path() . '/Customer.php');
    }

    public function testCreateMigrationCommand()
    {
        $this->artisan('create:migration', [
            'name' => 'customers',
            '--schema' => 'name#string; email#email',
        ]);

        $this->assertContains('Migration created successfully.', $this->consoleOutput());
    }

    public function testCreateViewCommand()
    {
        $this->artisan('create:view', [
            'name' => 'customers',
            '--fields' => "title#string; body#text",
        ]);

        $this->assertContains('View created successfully.', $this->consoleOutput());

        $this->assertDirectoryExists(config('view.paths')[0] . '/customers');
    }
}
