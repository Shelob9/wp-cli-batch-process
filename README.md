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
