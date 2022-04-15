# DragonCode\DocsGenerator\Helpers\Composer

### description

Gets the package description.

```php
use DragonCode\DocsGenerator\Helpers\Composer;

$instance = new Composer();

return $instance->description();
```


Execution result example:

```php
Document generation assistant.
```

### fullName

Gets the package full name from the composer.json file.

```php
use DragonCode\DocsGenerator\Helpers\Composer;

$instance = new Composer();

return $instance->fullName();
```


Execution result example:

```php
dragon-code/docs-generator
```

### ignoreNamespaces

Returns a list of ignored namespaces.

```php
use DragonCode\DocsGenerator\Helpers\Composer;

$instance = new Composer();

return $instance->ignoreNamespaces();
```


Execution result example:

```php
[
    'DragonCode\DocsGenerator\Console',
    'DragonCode\DocsGenerator\Dto',
    'DragonCode\DocsGenerator\Enum',
]
```

### namespaces

Returns a list of namespaces defined in the composer.json file.

```php
use DragonCode\DocsGenerator\Helpers\Composer;

$instance = new Composer();

return $instance->namespaces();
```


Execution result example:

```php
[
    'DragonCode\DocsGenerator\' => 'src'
]
```

### package

Gets the name of the application.

```php
use DragonCode\DocsGenerator\Helpers\Composer;

$instance = new Composer();

return $instance->package();
```


Execution result example:

```php
Docs Generator
```

### preview

Gets an object to build a preview.

```php
use DragonCode\DocsGenerator\Helpers\Composer;

$instance = new Composer();

return $instance->preview();
```


Execution result example:

```php
$preview = Preview::make(...);

$preview->brand;
// php, laravel, symfony, etc
// See more at https://dragon-code.pro

$preview->vendor;
// If the `extra.dragon-code.docs-generator.preview.brand` key is defined,
// then its value, otherwise the value up to the symbol `/` vendor key will be taken.
//
// For example,
//   the dragon code
//   dragon-code

$preview->name;
// Everything after the slash in the name key.
//
// For example,
//   docs-generator
```

### vendor

Gets the name of the vendor.

```php
use DragonCode\DocsGenerator\Helpers\Composer;

$instance = new Composer();

return $instance->vendor();
```


Execution result example:

```php
The Dragon Code
```


