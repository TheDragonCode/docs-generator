# DragonCode\DocsGenerator\Models\File

### getMarkdownFilename

Receive a generated path to markdown file.

```php
use DragonCode\DocsGenerator\Models\File;

$instance = new File();

return $instance->getMarkdownFilename();
```


Execution result example:

```php
models/file.md
```

### getNamespace

Receive a namespace.

```php
use DragonCode\DocsGenerator\Models\File;

$instance = new File();

return $instance->getNamespace();
```


Execution result example:

```php
\DragonCode\DocsGenerator\Models\File
```

### getShowNamespace

Receive display namespace.

```php
use DragonCode\DocsGenerator\Models\File;

$instance = new File();

return $instance->getShowNamespace();
```


Execution result example:

```php
DragonCode\DocsGenerator\Models\File
```


