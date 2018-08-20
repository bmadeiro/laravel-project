## Usage

Use --force to overwrite exist file on all command.

By default, the generator will attempt to append the route to your ```Route``` file. If you don't want the route added, you can use this option ```--route=no```.

If you chose not to add the crud route in automatically (see above), you will need to include the route manually.

```php
Route::resource('posts', 'PostsController');
```

## Create Command

1. Generate All (Migration, Model, Controller, Views):

  ```bash
  php artisan create:project
  ```

2. Generate Migration:

  ```bash
  php artisan create:migrate MigrationName
  ```

e.g.

  ```bash
  php artisan create:migrate create_posts_table
  ```

3. Generate Model:

  ```bash
  php artisan create:model ModelName
  ```

e.g.

  ```bash
  php artisan create:model Post
  ```

4. Generate Controller

  ```bash
  php artisan create:controller ControllerName
  ```

e.g.

  ```bash
  php artisan create:controller PostController
  ```

5. Generate View

  ```bash
  php artisan create:view ViewName
  ```

e.g.

  ```bash
  php artisan create:view index
  php artisan create:view create
  ```

6. Generate API Crud

```bash
php artisan create:api ApiName
```

e.g.

```bash
php artisan create:api Post
```

7. Generate API Controller

```bash
php artisan create:api-controller ApiName
```

e.g.

```bash
php artisan crud:api-controller Api\\PostsController
```

[&larr; Back to index](README.md)
