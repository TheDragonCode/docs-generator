# DragonCode\DocsGenerator\Helpers\Env

### get

Get the value of environment settings.

```php
use DragonCode\DocsGenerator\Helpers\Env;

$instance = new Env();

return $instance->get();
```


Execution result example:

```php
return Env::get('GITHUB_TOKEN')
// 02d95b05-0515-4480-91bf-37a7c86e2274
```


