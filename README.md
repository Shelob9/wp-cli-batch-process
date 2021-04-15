# WordPress CLI PLugin

WordPress plugin with WP CLI commands to 


## Add A Command

### JSON Args Paginated Delete

```php
add_filter( 'plugin_name_get_processors', function($processors){
	$processors['name_of_command'] = [
		'path/to/args.json',
		PluginNamespace::DeleteHandler
	];
	return $processors;
});
```

```bash
wp cli plugin-name run name_of_command
```

### JSON Args Some Other Handler
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

```bash
wp cli plugin-name run other_command
```

## Development

- Install
    - `git clone ...`
    - `composer install`
- Run unit tests
    - `compose test`
- Lint
    - `composer lint`
    - `composer fix`

### Integration Tests

These tests are dependent on WordPress and MySQL and are run with phpunit. The unit tests do not have any of these dependencies.


- Install tests with supplied docker-compose:
    - Using [futureys/phpunit-wordpress-plugin](https://hub.docker.com/r/futureys/phpunit-wordpress-plugin)
    - `docker-compose run phpunit`
        - This puts you inside phpunit container with database setup.
    - `composer install` # install for development inside of the container.
    - Run tests 
        - `composer test:wordpress`
- If supplying your own test setup and database:
    - `composer test:wordpress`