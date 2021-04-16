# WordPress CLI PLugin

WordPress plugin with WP CLI commands to batch process posts. There are:

- Commands whose input comes from WP_Query
- Commands whose input comes from CSV files with ID and slug.



## WP Query Commands

Use the `plugin_name_get_processors` filter to register these commands. You should provide the path to a JSON file with an array of `WP_Query` arguments and a reference to a class that impliments `PluginNamespace\RecivesResults`.

### JSON Args Paginated Delete

To delete all found posts, use the `PluginNamespace::DeleteHandler` class.

```php
add_filter( 'plugin_name_get_processors', function($processors){
	$processors['name_of_command'] = [
		'path/to/args.json',
		PluginNamespace::DeleteHandler
	];
	return $processors;
});
```


Since this the index of used above is "name_of_command", you can run this command with:

```bash
wp cli plugin-name run name_of_command
```

### JSON Args Some Other Handler

You can also write your own handler. Make sure to have a method called handle that returns true, unless there is an error, then it returns false.

```php
use PluginNamespace\RecivesResults;

class YourHandler implements RecivesResults {
    public function handle($results): bool
    {
        //Do something with array of posts
        return true;//Return false if an error happened
    }
}
add_filter( 'plugin_name_get_processors', function($processors){
	$processors['other_command'] = [
		'path/to/args.json',
		YourNamespace::YourHandler
	];
	return $processors;
});
```

Since this the index of used above is "other_command", you can run this command with:

```bash
wp cli plugin-name run other_command
```

## CSV Commands

Still Working on this.


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

### Using Docker For WP CLI

```sh
docker-compose run cli wp --version
```

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