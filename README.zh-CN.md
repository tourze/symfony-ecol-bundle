# symfony-ecol-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![最新版本](https://img.shields.io/packagist/v/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)
[![构建状态](https://img.shields.io/travis/tourze/symfony-ecol-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/symfony-ecol-bundle)
[![代码质量](https://img.shields.io/scrutinizer/g/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/symfony-ecol-bundle)
[![下载总量](https://img.shields.io/packagist/dt/tourze/symfony-ecol-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-ecol-bundle)

此 Bundle 为 Symfony 应用提供增强的表达式语言体验，包括额外的函数、值提供者以及中文语法支持。

## 功能特性

- 增强的 Symfony 表达式语言引擎，支持中文语法
- 为表达式提供日期和数学函数
- 通过 Doctrine 事件订阅器自动验证表达式
- 常用日期操作的值提供者（今天、当前时间戳等）
- 支持自定义表达式函数和值提供者
- 实体属性表达式验证的特性注解

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM

## 安装

使用 Composer 安装此 Bundle：

```bash
composer require tourze/symfony-ecol-bundle
```

在 `config/bundles.php` 中注册此 Bundle：

```php
return [
    // ...
    Tourze\EcolBundle\EcolBundle::class => ['all' => true],
    // ...
];
```

## 快速开始

### 基本用法

在代码中直接使用引擎服务：

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
        // 基本表达式
        $result = $this->engine->evaluate('1 + 1');
        
        // 带变量的表达式
        $result = $this->engine->evaluate('a + b', ['a' => 5, 'b' => 10]);
        
        // 带中文语法的表达式
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

### 验证实体属性

使用 `Expression` 特性注解验证实体属性中的表达式：

```php
use Tourze\EcolBundle\Attribute\Expression;

class YourEntity
{
    #[Expression]
    private string $conditionExpression = 'a > b && c == 1';
    
    // ...
}
```

## 可用的函数提供者

- `DateFunctionProvider`：日期操作函数
- `MathFunctionProvider`：数学运算
- `ExceptionFunctionProvider`：表达式中的异常处理
- `ServiceFunctionProvider`：在表达式中访问服务

## 可用的值提供者

- `Today`：提供今天的日期
- `TodayRange`：提供今天的开始和结束时间戳
- `CurrentTimestamp`：提供当前时间戳
- `CurrentYear`：提供当前年份
- `CurrentMonth`：提供当前月份
- `CurrentWeekday0` - `CurrentWeekday6`：提供星期几的标志

## 中文语法支持

引擎会自动将中文运算符转换为对应的编程语言等价物：

| 中文 | 编程语言 |
|------|----------|
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

## 贡献

详情请参见 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 许可证

MIT 许可证 (MIT)。详情请查看 [License 文件](LICENSE)。 