# Command

```php
<?php

use Chubbyphp\Config\Command\CleanDirectoriesCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

$input = new ArgvInput();

$console = new Application();
$console->add(
    new CleanDirectoriesCommand($container['chubbyphp.config.directories']) // when using with the ConfigServiceProvider
);
$console->run($input);
```

```bash
/path/to/console config:clean-directories cache log
```
