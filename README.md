# symfony-ecol-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)
[![License](https://img.shields.io/packagist/l/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)

[![Build Status](https://img.shields.io/travis/tourze/symfony-ecol-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/symfony-ecol-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/symfony-ecol-bundle)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/symfony-ecol-bundle/code-structure)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)

This bundle provides an enhanced Expression Language experience for Symfony applications 
with additional functions, values, and Chinese syntax support.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Requirements](#requirements)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [Available Function Providers](#available-function-providers)
- [Available Value Providers](#available-value-providers)
- [Chinese Syntax Support](#chinese-syntax-support)
- [Advanced Usage](#advanced-usage)
- [Contributing](#contributing)
- [License](#license)

## Features

- Enhanced Symfony Expression Language engine with Chinese syntax support
- Date and math function providers for expressions
- Automatic expression validation via Doctrine event subscribers
- Value providers for common date operations (today, current timestamp, etc.)
- Support for custom expression functions and value providers
- Attribute for expression validation on entity properties

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

## Requirements

- PHP 8.2 or higher
- Symfony 7.3 or higher  
- Doctrine ORM

## Configuration

The bundle works out of the box without additional configuration. However, you can 
customize it by creating custom function providers and value providers.

### Custom Function Providers

Create a service that implements function provider interface and tag it:

```yaml
# config/services.yaml
services:
    App\Expression\MyFunctionProvider:
        tags: ['ecol.function.provider']
```

### Custom Value Providers  

Create a service that implements value provider interface and tag it:

```yaml
# config/services.yaml
services:
    App\Expression\MyValueProvider:
        tags: ['ecol.value.provider']
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

- `Today`: Provides today's date (variable name: `当天日期`)
- `TodayRange`: Provides today's start and end timestamps (variable name: `当天日期范围`)
- `CurrentTimestamp`: Provides current timestamp (variable name: `当前时间戳`)
- `CurrentYear`: Provides current year
- `CurrentMonth`: Provides current month
- `CurrentWeekday0` - `CurrentWeekday6`: Provides weekday dates (variable name: `本周周日日期` etc.)

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

## Advanced Usage

### Working with Complex Expressions

For complex business logic, you can combine multiple operators and functions:

```php
$expression = '(age >= 18 并且 country == "CN") 或者 (vip_level > 3 且 balance >= 1000)';
$result = $engine->evaluate($expression, [
    'age' => 25,
    'country' => 'CN', 
    'vip_level' => 2,
    'balance' => 500
]);
```

### Custom Expression Functions

Implement the ExpressionFunctionProviderInterface to add custom functions:

```php
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class CustomFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('custom_function', 
                function ($arg) { return sprintf('custom_function(%s)', $arg); },
                function ($arguments, $arg) { return $arg * 2; }
            ),
        ];
    }
}
```

### Entity Validation with Complex Rules

Use expressions for complex entity validation rules:

```php
#[Expression]
private string $businessRule = 'status == "active" && (priority > 5 || urgent == true)';
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
