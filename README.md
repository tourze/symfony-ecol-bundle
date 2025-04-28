# symfony-ecol-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)
[![Build Status](https://img.shields.io/travis/tourze/symfony-ecol-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/symfony-ecol-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/symfony-ecol-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)

This bundle provides an enhanced Expression Language experience for Symfony applications with additional functions, values, and Chinese syntax support.

## Features

- Enhanced Symfony Expression Language engine with Chinese syntax support
- Date and math function providers for expressions
- Automatic expression validation via Doctrine event subscribers
- Value providers for common date operations (today, current timestamp, etc.)
- Support for custom expression functions and value providers
- Attribute for expression validation on entity properties

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM

## Installation

To install the bundle, require it using Composer:

```bash
composer require tourze/symfony-ecol-bundle
```

Register the bundle in your `config/bundles.php`:

```php
return [
    // ...
    Tourze\EcolBundle\EcolBundle::class => ['all' => true],
    // ...
];
```

## Quick Start

### Basic Usage

Use the engine service directly in your code:

```php
use Symfony\Component\ExpressionLanguage\Expression;
use Tourze\EcolBundle\Service\Engine;

class YourService
{
    public function __construct(
        private readonly Engine $engine
    ) {
    }
    
    public function evaluateExpression(): mixed
    {
        // Basic expression
        $result = $this->engine->evaluate('1 + 1');
        
        // Expression with variables
        $result = $this->engine->evaluate('a + b', ['a' => 5, 'b' => 10]);
        
        // Expression with Chinese syntax
        $result = $this->engine->evaluate('a 大于 b 并且 c 等于 d', [
            'a' => 10, 
            'b' => 5, 
            'c' => 'test', 
            'd' => 'test'
        ]);
        
        return $result;
    }
}
```

### Validate Entity Properties

Use the `Expression` attribute to validate expressions in entity properties:

```php
use Tourze\EcolBundle\Attribute\Expression;

class YourEntity
{
    #[Expression]
    private string $conditionExpression = 'a > b && c == 1';
    
    // ...
}
```

## Available Function Providers

- `DateFunctionProvider`: Date manipulation functions
- `MathFunctionProvider`: Mathematical operations
- `ExceptionFunctionProvider`: Exception handling in expressions
- `ServiceFunctionProvider`: Service access in expressions

## Available Value Providers

- `Today`: Provides today's date
- `TodayRange`: Provides today's start and end timestamps
- `CurrentTimestamp`: Provides current timestamp
- `CurrentYear`: Provides current year
- `CurrentMonth`: Provides current month
- `CurrentWeekday0` - `CurrentWeekday6`: Provides weekday flags

## Chinese Syntax Support

The engine automatically converts Chinese operators to their programming equivalents:

| Chinese | Programming |
|---------|-------------|
| 并且, 并, 与, 和 | && |
| 或者, 或 | \|\| |
| 不是, 不等于 | != |
| 等于, 相等于, 是 | == |
| 全等于 | === |
| 大于, 多于 | > |
| 小于, 少于 | < |
| 大于等于, 大于或等于 | >= |
| 小于等于, 小于或等于 | <= |
| 加上 | + |
| 减去 | - |
| 乘以 | * |
| 除以 | / |

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
