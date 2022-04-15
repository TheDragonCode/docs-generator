# DragonCode\DocsGenerator\Services\Package

### description

Gets the package description.

```php
use DragonCode\DocsGenerator\Services\Package;

$instance = new Package();

return $instance->description();
```


Execution result example:

```php
Document generation assistant.
```

### files



```php
use DragonCode\DocsGenerator\Services\Package;

$instance = new Package();

return $instance->files();
```



### fullName

Gets the package full name from the composer.json file.

```php
use DragonCode\DocsGenerator\Services\Package;

$instance = new Package();

return $instance->fullName();
```


Execution result example:

```php
dragon-code/docs-generator
```

### package

Gets the name of the application.

```php
use DragonCode\DocsGenerator\Services\Package;

$instance = new Package();

return $instance->package();
```


Execution result example:

```php
Docs Generator
```

### preview

Returns an Preview instance.

```php
use DragonCode\DocsGenerator\Services\Package;

$instance = new Package();

return $instance->preview();
```



### vendor

Gets the name of the vendor.

```php
use DragonCode\DocsGenerator\Services\Package;

$instance = new Package();

return $instance->vendor();
```


Execution result example:

```php
The Dragon Code
```


