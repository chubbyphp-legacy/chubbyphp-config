# chubbyphp-config

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-config.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-config)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-config/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-config?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-config/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-config)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-config/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-config)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-config/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-config)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-config/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-config)

## Description

A simple config.

## Requirements

 * php: ^7.2

## Suggest

 * pimple/pimple: ^3.2.3
 * symfony/console: ^2.8|^3.4|^4.2|^5.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-config][1].

```bash
composer require chubbyphp/chubbyphp-config "^2.0"
```

## Usage

### Command

 * [CleanDirectoriesCommand][2]

### Bootstrap

```php
<?php

namespace MyProject;

use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\Pimple\ConfigServiceProvider;
use MyProject\Config\DevConfig;
use MyProject\Config\ProdConfig;
use Pimple\Container;

$configProvider = new ConfigProvider(__DIR__, [
    new DevConfig(__DIR__),
    new ProdConfig(__DIR__),
]);

$container = new Container(['env' => 'dev']);
$container->register(new ConfigServiceProvider($configProvider));
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

Dominik Zogg 2019

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-config
[2]: doc/Command/CleanDirectoriesCommand.md
