<?php

namespace Tourze\EcolBundle\Tests\Attribute;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Attribute\Expression;

/**
 * @internal
 */
#[CoversClass(Expression::class)]
final class ExpressionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // 这个测试不需要特殊的设置
    }

    public function testExpressionShouldBeAttributeOnProperty(): void
    {
        // 定义一个带有Expression属性的类
        $testEntity = new class {
            #[Expression]
            private string $condition = 'a > b';

            public function getCondition(): string
            {
                return $this->condition;
            }
        };

        // 通过反射检查属性上的属性
        $reflection = new \ReflectionClass($testEntity);
        $property = $reflection->getProperty('condition');
        $attributes = $property->getAttributes(Expression::class);

        // 断言属性存在并且是表达式属性
        $this->assertCount(1, $attributes);
        $this->assertEquals(Expression::class, $attributes[0]->getName());

        // 实例化属性并检查其类型
        $expression = $attributes[0]->newInstance();
        $this->assertInstanceOf(Expression::class, $expression);
    }

    public function testExpressionShouldHaveTargetProperty(): void
    {
        // 通过反射检查属性的目标
        $reflection = new \ReflectionClass(Expression::class);
        $attributes = $reflection->getAttributes();

        // 断言存在目标设置为PROPERTY的属性
        $this->assertCount(1, $attributes);
        $this->assertEquals(\Attribute::class, $attributes[0]->getName());

        // 检查属性的参数
        $attributeArgs = $attributes[0]->getArguments();
        $this->assertCount(1, $attributeArgs);
        $this->assertEquals(\Attribute::TARGET_PROPERTY, $attributeArgs[0]);
    }
}
