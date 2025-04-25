# symfony-ecol-bundle

[English](mdc:README.md) | [中文](mdc:README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)
[![Build Status](https://img.shields.io/travis/tourze/symfony-ecol-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/symfony-ecol-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/symfony-ecol-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)

This bundle provides an integration of ecol services into Symfony framework.

## Features

- Easy integration of ecol services
- Flexible configuration options
- Customizable templates

## Installation

To install the bundle, require it using Composer:

```bash
composer require tourze/symfony-ecol-bundle
```

## Quick Start

To get started with this bundle, add the service to your AppKernel.php:

```php
public function registerBundles()
{
    $bundles = [
        // ...
        new Tourze\Bundle\EcolBundle\EcolBundle(),
        // ...
    ];

    return $bundles;
}
```

Then you can use the service:

```php
public function doSomething()
{
    $service = $container->get('ecol.service');
    // Use the service...
}
```

## Documentation

Detailed documentation is available at the official documentation section.

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
