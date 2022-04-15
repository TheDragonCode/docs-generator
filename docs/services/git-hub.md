# DragonCode\DocsGenerator\Services\GitHub

### all

Get the entire list of repositories for the selected organization.

```php
use DragonCode\DocsGenerator\Services\GitHub;

$instance = new GitHub();

return $instance->all();
```



### download

Clone repository for further processing.

```php
use DragonCode\DocsGenerator\Services\GitHub;

$instance = new GitHub();

return $instance->download();
```



### repositories

Get a filtered list of repositories for the selected organization.

```php
use DragonCode\DocsGenerator\Services\GitHub;

$instance = new GitHub();

return $instance->repositories();
```




