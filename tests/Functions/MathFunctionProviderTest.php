<?php

namespace Tourze\EcolBundle\Tests\Functions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tourze\EcolBundle\Functions\MathFunctionProvider;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(MathFunctionProvider::class)]
#[RunTestsInSeparateProcesses]
final class MathFunctionProviderTest extends AbstractIntegrationTestCase
{
    private MathFunctionProvider $provider;

    private ExpressionLanguage $expressionLanguage;

    protected function onSetUp(): void
    {
        $this->provider = self::getService(MathFunctionProvider::class);
        $this->expressionLanguage = new ExpressionLanguage();
        $this->expressionLanguage->registerProvider($this->provider);
    }

    public function testGetFunctionsShouldReturnExpressionFunctionArray(): void
    {
        $functions = $this->provider->getFunctions();
        $this->assertNotEmpty($functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf(ExpressionFunction::class, $function);
        }

        // 确保包含指定的函数
        $functionNames = array_map(fn ($func) => $func->getName(), $functions);
        $this->assertContains('取绝对值', $functionNames);
        $this->assertContains('取负数', $functionNames);
        $this->assertContains('加上', $functionNames);
    }

    public function testAbsoluteValueFunctionShouldReturnAbsoluteValue(): void
    {
        $result = $this->expressionLanguage->evaluate('取绝对值(-5)');
        $this->assertEquals(5, $result);

        $result = $this->expressionLanguage->evaluate('取绝对值(5)');
        $this->assertEquals(5, $result);

        $result = $this->expressionLanguage->evaluate('取绝对值(0)');
        $this->assertEquals(0, $result);
    }

    public function testNegativeValueFunctionShouldReturnNegativeValue(): void
    {
        $result = $this->expressionLanguage->evaluate('取负数(5)');
        $this->assertEquals(-5, $result);

        $result = $this->expressionLanguage->evaluate('取负数(-5)');
        $this->assertEquals(-5, $result); // 应该返回-|-5| = -5

        $result = $this->expressionLanguage->evaluate('取负数(0)');
        $this->assertEquals(0, $result);
    }

    public function testAddFunctionShouldAddTwoNumbers(): void
    {
        $result = $this->expressionLanguage->evaluate('加上(5, 3)');
        $this->assertEquals(8, $result);

        $result = $this->expressionLanguage->evaluate('加上(-5, 3)');
        $this->assertEquals(-2, $result);

        $result = $this->expressionLanguage->evaluate('加上(0, 0)');
        $this->assertEquals(0, $result);
    }

    public function testAddFunctionShouldConvertStringToNumber(): void
    {
        $result = $this->expressionLanguage->evaluate('加上("5", "3")');
        $this->assertEquals(8, $result);
        $this->assertIsFloat($result);

        $result = $this->expressionLanguage->evaluate('加上("5.5", "3.3")');
        $this->assertEquals(8.8, $result);
        $this->assertIsFloat($result);
    }

    public function testAddFunctionShouldHandleInvalidInput(): void
    {
        // 非数字字符串会被转换为0
        $result = $this->expressionLanguage->evaluate('加上("abc", 5)');
        $this->assertEquals(5, $result);
    }
}
