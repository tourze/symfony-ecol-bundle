<?php

namespace Tourze\EcolBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EcolBundle\Service\Engine;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(Engine::class)]
#[RunTestsInSeparateProcesses]
final class EngineTest extends AbstractIntegrationTestCase
{
    private Engine $engine;

    protected function onSetUp(): void
    {
        // 获取引擎服务
        $this->engine = self::getService(Engine::class);
    }

    public function testBasicExpressionShouldEvaluateSimpleExpression(): void
    {
        $result = $this->engine->evaluate('1 + 1');
        $this->assertEquals(2, $result);
    }

    public function testEvaluateWithVariablesShouldUseProvidedVariables(): void
    {
        $result = $this->engine->evaluate('a + b', ['a' => 5, 'b' => 10]);
        $this->assertEquals(15, $result);
    }

    public function testChineseOperatorShouldConvertToStandardOperator(): void
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

    public function testEnvironmentVariablesShouldBeAccessible(): void
    {
        // 使用数组测试，而不是对象属性访问
        $_ENV['TEST_VAR'] = 'test_value';
        $result = $this->engine->evaluate("env['TEST_VAR'] == 'test_value'");
        $this->assertTrue($result);
    }

    public function testSecurityShouldProvideAuthenticationSupport(): void
    {
        // 测试 token 变量是否存在（无论是否为 null）
        $result = $this->engine->evaluate('token === null or token !== null');
        $this->assertTrue($result);
    }

    public function testPrepareValuesShouldProcessValueFunctions(): void
    {
        $valueFunctions = [
            'test_func' => function ($values) {
                return 'test_value';
            },
        ];

        $values = [];
        $expression = 'test_func == "test_value"';

        $preparedValues = $this->engine->prepareValues($expression, $values, $valueFunctions);

        $this->assertArrayHasKey('test_func', $preparedValues);
        $this->assertEquals('test_value', $preparedValues['test_func']);
    }

    public function testPrepareValuesShouldSkipUnusedValueFunctions(): void
    {
        $valueFunctions = [
            'unused_func' => function ($values) {
                return 'unused_value';
            },
        ];

        $values = [];
        $expression = 'test_func == "test_value"';

        $preparedValues = $this->engine->prepareValues($expression, $values, $valueFunctions);

        $this->assertArrayNotHasKey('unused_func', $preparedValues);
    }
}
