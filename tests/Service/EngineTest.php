<?php

namespace Tourze\EcolBundle\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tourze\EcolBundle\Service\Engine;
use Tourze\EcolBundle\Value\ExpressionValue;

class EngineTest extends TestCase
{
    private Engine $engine;
    private MockObject $security;
    private MockObject $logger;
    private MockObject $functionProvider;
    private MockObject $valueProvider;
    private array $functionProviders;
    private array $valueProviders;

    protected function setUp(): void
    {
        // 模拟函数提供者
        $this->functionProvider = $this->createMock(ExpressionFunctionProviderInterface::class);
        $this->functionProvider->method('getFunctions')->willReturn([
            new ExpressionFunction('test_func', 
                fn($arg) => sprintf('test_func(%s)', $arg),
                fn($values, $arg) => "test_{$arg}"
            ),
        ]);
        $this->functionProviders = [$this->functionProvider];

        // 模拟值提供者
        $this->valueProvider = $this->createMock(ExpressionValue::class);
        $this->valueProviders = [$this->valueProvider];

        // 模拟安全服务
        $this->security = $this->createMock(Security::class);
        $token = $this->createMock(TokenInterface::class);
        $this->security->method('getToken')->willReturn($token);

        // 模拟日志服务
        $this->logger = $this->createMock(LoggerInterface::class);

        // 创建引擎实例
        $this->engine = new Engine(
            $this->functionProviders,
            $this->valueProviders,
            $this->security,
            $this->logger
        );
    }

    public function testBasicExpression_shouldEvaluateSimpleExpression(): void
    {
        $result = $this->engine->evaluate('1 + 1');
        $this->assertEquals(2, $result);
    }

    public function testEvaluateWithVariables_shouldUseProvidedVariables(): void
    {
        $result = $this->engine->evaluate('a + b', ['a' => 5, 'b' => 10]);
        $this->assertEquals(15, $result);
    }

    public function testChineseOperator_shouldConvertToStandardOperator(): void
    {
        // 测试"并且"转换为"&&"
        $result = $this->engine->evaluate('1 > 0 并且 2 > 1');
        $this->assertTrue($result);
        
        // 测试"或者"转换为"||"
        $result = $this->engine->evaluate('1 > 2 或者 2 > 1');
        $this->assertTrue($result);
        
        // 测试"大于"转换为">"
        $result = $this->engine->evaluate('2 大于 1');
        $this->assertTrue($result);
        
        // 测试"小于"转换为"<"
        $result = $this->engine->evaluate('1 小于 2');
        $this->assertTrue($result);
        
        // 测试"等于"转换为"=="
        $result = $this->engine->evaluate('1 等于 1');
        $this->assertTrue($result);
        
        // 测试"不等于"转换为"!="
        $result = $this->engine->evaluate('1 不等于 2');
        $this->assertTrue($result);
    }

    public function testValueProvider_shouldInjectValues(): void
    {
        $testValue = 'test_value';
        $testValueName = 'test_name';
        
        // 配置值提供者
        $this->valueProvider->method('isSupported')->willReturn(true);
        $this->valueProvider->method('getNames')->willReturn([$testValueName]);
        $this->valueProvider->method('getValue')->willReturn($testValue);
        
        // 测试值是否正确注入到表达式中
        $result = $this->engine->evaluate("test_name == '{$testValue}'");
        $this->assertTrue($result);
    }

    public function testValueProvider_shouldHandleValueProviderException(): void
    {
        // 配置值提供者抛出异常
        $this->valueProvider->method('isSupported')->willThrowException(new \RuntimeException('Test exception'));
        
        // 确保日志服务记录错误
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('处理额外value时发生错误'),
                $this->callback(function ($context) {
                    return isset($context['exception']) && $context['exception'] instanceof \RuntimeException;
                })
            );
        
        // 测试引擎可以处理异常并继续执行
        $result = $this->engine->evaluate('1 + 1');
        $this->assertEquals(2, $result);
    }

    public function testEnvironmentVariables_shouldBeAccessible(): void
    {
        // 使用数组测试，而不是对象属性访问
        $_ENV['TEST_VAR'] = 'test_value';
        $result = $this->engine->evaluate("env['TEST_VAR'] == 'test_value'");
        $this->assertTrue($result);
    }

    public function testSecurity_shouldProvideAuthenticationSupport(): void
    {
        // 模拟token不为null的情况
        $result = $this->engine->evaluate('token != null');
        $this->assertTrue($result);
    }

    public function testPrepareValues_shouldProcessValueFunctions(): void
    {
        $valueFunctions = [
            'test_func' => function($values) {
                return 'test_value';
            }
        ];
        
        $values = [];
        $expression = 'test_func == "test_value"';
        
        $preparedValues = $this->engine->prepareValues($expression, $values, $valueFunctions);
        
        $this->assertArrayHasKey('test_func', $preparedValues);
        $this->assertEquals('test_value', $preparedValues['test_func']);
    }

    public function testPrepareValues_shouldSkipUnusedValueFunctions(): void
    {
        $valueFunctions = [
            'unused_func' => function($values) {
                return 'unused_value';
            }
        ];
        
        $values = [];
        $expression = 'test_func == "test_value"';
        
        $preparedValues = $this->engine->prepareValues($expression, $values, $valueFunctions);
        
        $this->assertArrayNotHasKey('unused_func', $preparedValues);
    }
} 