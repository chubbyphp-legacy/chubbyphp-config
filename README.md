# chubbyphp-config

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-config.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-config)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-config/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-config/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-config/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-config/?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-config/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-config)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-config/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-config)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-config/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-config)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-config/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-config)

## Description

A simple config.

## Requirements

 * php: ~7.0

## Suggest

 * pimple/pimple: ~3.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-config][1].

```sh
composer require chubbyphp/chubbyphp-config "~1.0"
```

## Usage

### Without container

```php
<?php

namespace MyProject;

use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ConfigMapping;
use MyProject\Config\DevConfig;

$configProvider = new ConfigProvider(__DIR__, [
    new ConfigMapping('dev', DevConfig::class),
]);

$config = $configProvider->get('dev');
```

### With Pimple

```php
<?php

namespace MyProject;

use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ConfigMapping;
use Chubbyphp\Config\Pimple\ConfigServiceProvider;
use MyProject\Config\DevConfig;
use Pimple\Container;

$configProvider = new ConfigProvider(__DIR__, [
    new ConfigMapping('dev', DevConfig::class),
]);

$container = new Container(['env' => 'dev']);

$configServiceProvider = new ConfigServiceProvider($configProvider);
$configServiceProvider->register($container);
```

### With Slim

```php
<?php

namespace MyProject;

use Chubbyphp\Config\ConfigProvider;
use Chubbyphp\Config\ConfigMapping;
use Chubbyphp\Config\Pimple\ConfigServiceProvider;
use Chubbyphp\Config\Slim\SlimSettingsServiceProvider;
use MyProject\Config\DevConfig;
use Pimple\Container;

$configProvider = new ConfigProvider(__DIR__, [
    new ConfigMapping('dev', DevConfig::class),
]);

$container = new Container(['env' => 'dev']);

$configServiceProvider = new ConfigServiceProvider($configProvider);
$configServiceProvider->register($container);

$slimSettingsProvider = new SlimSettingsServiceProvider($configProvider);
$slimSettingsProvider->register($container);
```

## Copyright

Dominik Zogg 2018

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-config
