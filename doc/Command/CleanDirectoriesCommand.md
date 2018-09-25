# Command

```php
<?php

use Chubbyphp\Config\Command\CleanDirectoriesCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

$input = new ArgvInput();

$console = new Application();
$console->add(
    new CleanDirectoriesCommand([
        'cache' => __DIR__ . '/var/cache',
        'log' => __DIR__ . '/var/log'
    ])
);
$console->run($input);
```

```bash
/path/to/console config:clean-directories cache log
```
