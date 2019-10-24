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
 * slim/slim: ^3.0
 * symfony/console: ^2.8|^3.4|^4.2

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-config][1].

```bash
composer require chubbyphp/chubbyphp-config "^1.3"
```

## Usage

### Command

 * [CleanDirectoriesCommand][2]

### Bootstrap

```php
<?php

namespace MyProject;

use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ConfigMapping;
use Chubbyphp\Config\Pimple\ConfigServiceProvider;
use MyProject\Config\DevConfig;
use MyProject\Config\ProdConfig;
use Pimple\Container;

$configProvider = new ConfigProvider(__DIR__, [
    new ConfigMapping('dev', DevConfig::class),
    new ConfigMapping('prod', ProdConfig::class),
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

    private function __construct()
    {
    }

    /**
     * @param string $rootDir
     *
     * @return self
     */
    public static function create(string $rootDir): ConfigInterface
    {
        $config = new self;
        $config->rootDir = $rootDir;

        return $config;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'env' => 'dev',
            'rootDir' => $this->rootDir
        ];
    }

    /**
     * @return array
     */
    public function getDirectories(): array
    {
        return [
            'cache' => $this->rootDir . '/var/cache/dev',
            'logs' => $this->rootDir . '/var/logs/dev',
        ];
    }
}
```

## Copyright

Dominik Zogg 2019

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-config
[2]: doc/Command/CleanDirectoriesCommand.md
