# chubbyphp-config

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-config.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-config)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-config/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-config?branch=master)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-config/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-config)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-config/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-config)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-config/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-config)
[![Daily Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-config/d/daily)](https://packagist.org/packages/chubbyphp/chubbyphp-config)

## Description

A simple config.

## Requirements

 * php: ^7.2

## Suggest

 * chubbyphp/chubbyphp-container: ^1.0
 * pimple/pimple: ^3.2.3
 * symfony/console: ^2.8.50|^3.4.26|^4.2.7|^5.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-config][1].

```bash
composer require chubbyphp/chubbyphp-config "^2.1"
```

## Usage

### Command

 * [CleanDirectoriesCommand][2]

### Bootstrap

#### ServiceFactory (chubbyphp/chubbyphp-container)

```php
<?php

namespace MyProject;

use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ServiceFactory\ConfigServiceFactory;
use Chubbyphp\Container\Container;
use MyProject\Config\DevConfig;
use MyProject\Config\ProdConfig;

$env = 'dev';

$container = new Container();
$container->factories((new ConfigServiceFactory((new ConfigProvider([
    new DevConfig(__DIR__.'/..'),
    new ProdConfig(__DIR__.'/..'),
]))->get($env)))());
```

#### ServiceProvider (pimple/pimple)

```php
<?php

namespace MyProject;

use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ServiceProvider\ConfigServiceProvider;
use MyProject\Config\DevConfig;
use MyProject\Config\ProdConfig;
use Pimple\Container;

$env = 'dev';

$container = new Container();
$container->register(new ConfigServiceProvider(
    (new ConfigProvider([
        new DevConfig(__DIR__.'/..'),
        new ProdConfig(__DIR__.'/..'),
    ]))->get($env)
));
```

### Config

```php
<?php

namespace MyProject\Config;

use Chubbyphp\Config\ConfigInterface;

class DevConfig implements ConfigInterface
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'env' => $this->getEnv(),
            'rootDir' => $this->rootDir
        ];
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        $environment = $this->getEnv();

        return [
            'cache' => $this->rootDir . '/var/cache/' . $environment,
            'logs' => $this->rootDir . '/var/logs/' . $environment,
        ];
    }

    public function getEnv(): string
    {
        return 'dev';
    }
}
```

## Copyright

Dominik Zogg 2020

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-config
[2]: doc/Command/CleanDirectoriesCommand.md
