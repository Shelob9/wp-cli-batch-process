# WordPress CLI Batch Process

WordPress plugin with wp commands to batch process posts. There are:

-[![PHP Unit Tests](https://github.com/Shelob9/wp-cli-batch-process/actions/workflows/php-unit.yml/badge.svg)](https://github.com/Shelob9/wp-cli-batch-process/actions/workflows/php-unit.yml)
[![WordPress Tests](https://github.com/Shelob9/wp-cli-batch-process/actions/workflows/wordpress.yml/badge.svg)](https://github.com/Shelob9/wp-cli-batch-process/actions/workflows/wordpress.yml)

## Comands

Delete Sample Content


```bash
wp batch-process batch delete-sample-content
```

## Command Types

- Commands whose input comes from WP_Query
- Commands whose input comes from CSV files with ID and slug.

Commands can be run 1 page at a time:

```bash
wp batch-process run name_of_command
# Optionally, set page and page size
wp batch-process run name_of_command --page=2 --perpage=10

```

Or as a batch:

```bash
wp batch-process batch name_of_command
wp batch-process batch name_of_command --perpage=50
```

### WP Query Commands

Use the `wp_cli_batch_process_get_processors` filter to register these commands. You should provide the path to a JSON file with an array of `WP_Query` arguments and a reference to a class that impliments `WpCliBatchProcess\RecivesResults`.

#### Example: Paginated Delete

To delete all found posts, use the `WpCliBatchProcess::DeleteHandler` class.

```php
add_filter( 'wp_cli_batch_process_get_processors', function($processors){
	$processors['name_of_command'] = [
        'type' => 'WP_QUERY',
		'source' => 'path/to/args.json',
		'handler' => 'WpCliBatchProcess::DeleteHandler'
	];
	return $processors;
});
```


Since this the index of used above is "name_of_command", you can run this command with:

```bash
wp batch-process run name_of_command
wp batch-process run name_of_command --page=2 --perpage=50
```

#### Example: Some Other Handler

You can also write your own handler. Make sure to have a method called handle that returns true, unless there is an error, then it returns false.

```php
use WpCliBatchProcess\RecivesResults;

class YourHandler implements RecivesResults {
    public function handle($results): bool
    {
        //Do something with array of posts
        return true;//Return false if an error happened
    }
}
add_filter( 'wp_cli_batch_process_get_processors', function($processors){
	$processors['other_command'] = [
        'type' => 'WP_Query',
		'source' => 'path/to/args.json',
		'handler' => YourNamespace::YourHandler
	];
	return $processors;
});
```

Since this the index of used above is "other_command", you can run this command with:

```bash
wp batch-process run other_command
wp batch-process run other_command --page=2 --perpage=50
```

### CSV Commands

#### Example Delete From A List Of Posts In CSV

```php
add_filter( 'wp_cli_batch_process_get_processors', function($processors){
    $processors['delete_something'] = [
            'type' => 'CSV',
            'source' => '/path/to/a.csv,'
            'handler' => WpCliBatchProcess::DeleteHandler
	];
	return $processors;
});
```


Since this the index of used above is "delete_something", you can run this command with:

```bash
wp batch-process run delete_something
wp batch-process run delete_something --page=2 --perpage=50
```

## Development

- Install
    - `git clone ...`
    - `composer install`
- Run unit tests
    - `compose test`
- Run WordPress tests
    - `compose test:wordprss`
- Lint
    - `composer lint`
    - `composer fix`

### PHP Unit Tests

These tests are located in `/tests/unit`

### WordPress Tests

These tests are dependent on WordPress and MySQL and are run with phpunit. The unit tests do not have any of these dependencies.

These tests are located in `/tests/integration`

- Install tests with supplied docker-compose:
    - Using [futureys/phpunit-wordpress-plugin](https://hub.docker.com/r/futureys/phpunit-wordpress-plugin)
    - `docker-compose run phpunit`
        - This puts you inside phpunit container with database setup.
    - `composer install` # install for development inside of the container.
    - Run tests 
        - `composer test:wordpress`
- If supplying your own test setup and database:
    - `composer test:wordpress`