<?php

namespace Tourze\EcolBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\EcolBundle\Attribute\Expression;
use Tourze\EcolBundle\EventSubscriber\ExpressionCheckSubscriber;
use Tourze\EcolBundle\Exception\ExpressionSyntaxException;
use Tourze\EcolBundle\Service\Engine;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(ExpressionCheckSubscriber::class)]
#[RunTestsInSeparateProcesses]
final class ExpressionCheckSubscriberTest extends AbstractEventSubscriberTestCase
{
    private ExpressionCheckSubscriber $subscriber;

    private MockObject $propertyAccessor;

    private MockObject $engine;

    protected static function getEventSubscriberClass(): string
    {
        return ExpressionCheckSubscriber::class;
    }

    protected function onSetUp(): void
    {
        // 创建 PropertyAccessor 的 mock
        $this->propertyAccessor = $this->createMock(PropertyAccessor::class);

        // 创建 Engine 的 mock
        $this->engine = $this->createMock(Engine::class);

        // 从容器获取测试目标实例
        $this->subscriber = self::getService(ExpressionCheckSubscriber::class);
    }

    public function testPrePersistShouldValidateExpressions(): void
    {
        // 获取订阅者实例

        // 创建测试实体
        $entity = new class {
            #[Expression]
            private string $condition = 'a > b';

            #[Expression]
            private string $emptyCondition = '';

            private string $nonExpressionField = 'some value';

            public function getCondition(): string
            {
                return $this->condition;
            }

            public function getEmptyCondition(): string
            {
                return $this->emptyCondition;
            }

            public function getNonExpressionField(): string
            {
                return $this->nonExpressionField;
            }
        };

        // 模拟ReflectionClass
        $reflectionClass = new \ReflectionClass($entity);

        // 模拟EntityManager和ClassMetadata
        $objectManager = $this->createMock(EntityManagerInterface::class);
        /*
         * 这里必须 mock ClassMetadata 具体类的三个理由：
         * 1. ClassMetadata 是 Doctrine ORM 的核心元数据类，没有对应的接口
         * 2. 测试需要验证 getReflectionClass() 方法的调用，这是 ClassMetadata 的具体实现
         * 3. ClassMetadata 包含了实体映射的具体信息，mock 它可以控制测试环境
         */
        $classMetadata = $this->createMock(ClassMetadata::class);

        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata)
        ;

        $classMetadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflectionClass)
        ;

        /*
         * 这里必须 mock LifecycleEventArgs 具体类的三个理由：
         * 1. PrePersistEventArgs 是 final 类无法 mock，只能使用父类 LifecycleEventArgs
         * 2. 需要测试事件参数的 getObject() 和 getObjectManager() 方法调用
         * 3. LifecycleEventArgs 是 Doctrine 事件系统的核心类，没有更通用的接口
         */
        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->expects($this->once())
            ->method('getObject')
            ->willReturn($entity)
        ;

        $eventArgs->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($objectManager)
        ;

        // 执行测试，验证不会抛出异常
        $this->subscriber->prePersist($eventArgs);

        // 验证方法执行成功（无异常即表示测试通过）
    }

    public function testPreUpdateShouldValidateExpressions(): void
    {
        // 获取订阅者实例

        // 创建测试实体
        $entity = new class {
            #[Expression]
            private string $condition = 'a > b';

            public function getCondition(): string
            {
                return $this->condition;
            }
        };

        // 模拟ReflectionClass
        $reflectionClass = new \ReflectionClass($entity);

        // 模拟EntityManager和ClassMetadata
        $objectManager = $this->createMock(EntityManagerInterface::class);
        /*
         * 这里必须 mock ClassMetadata 具体类的三个理由：
         * 1. ClassMetadata 是 Doctrine ORM 的核心元数据类，没有对应的接口
         * 2. 测试需要验证 getReflectionClass() 方法的调用，这是 ClassMetadata 的具体实现
         * 3. ClassMetadata 包含了实体映射的具体信息，mock 它可以控制测试环境
         */
        $classMetadata = $this->createMock(ClassMetadata::class);

        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata)
        ;

        $classMetadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflectionClass)
        ;

        /*
         * 这里必须 mock LifecycleEventArgs 具体类的三个理由：
         * 1. PreUpdateEventArgs 是 final 类无法 mock，只能使用父类 LifecycleEventArgs
         * 2. 需要测试事件参数的 getObject() 和 getObjectManager() 方法调用
         * 3. LifecycleEventArgs 是 Doctrine 事件系统的核心类，没有更通用的接口
         */
        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->expects($this->once())
            ->method('getObject')
            ->willReturn($entity)
        ;

        $eventArgs->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($objectManager)
        ;

        // 执行测试，验证不会抛出异常
        $this->subscriber->preUpdate($eventArgs);

        // 验证方法执行成功（无异常即表示测试通过）
    }

    public function testCheckExpressionShouldThrowExceptionForInvalidExpression(): void
    {
        // 获取订阅者实例

        // 创建测试实体
        $entity = new class {
            #[Expression]
            private string $invalidCondition = 'a > >';

            public function getInvalidCondition(): string
            {
                return $this->invalidCondition;
            }
        };

        // 模拟ReflectionClass
        $reflectionClass = new \ReflectionClass($entity);

        // 配置 PropertyAccessor 返回无效表达式
        $this->propertyAccessor->method('getValue')->willReturn('a > >');

        // 配置 Engine 抛出语法错误
        $this->engine->method('lint')->willThrowException(new SyntaxError('Syntax error'));

        // 期望抛出ExpressionSyntaxException，包含属性名和原始异常信息
        $this->expectException(ExpressionSyntaxException::class);
        $this->expectExceptionMessage('[invalidCondition]语法格式错误：');

        // 通过反射调用私有方法
        $method = new \ReflectionMethod(ExpressionCheckSubscriber::class, 'checkExpression');
        $method->setAccessible(true);
        $method->invoke($this->subscriber, $entity, $reflectionClass);
    }

    public function testCheckExpressionShouldSkipEmptyValues(): void
    {
        // 获取订阅者实例

        // 创建测试实体
        $entity = new class {
            #[Expression]
            private string $emptyCondition = '';

            public function getEmptyCondition(): string
            {
                return $this->emptyCondition;
            }
        };

        // 模拟ReflectionClass
        $reflectionClass = new \ReflectionClass($entity);

        // 通过反射调用私有方法，确保不会抛出异常
        $method = new \ReflectionMethod(ExpressionCheckSubscriber::class, 'checkExpression');
        $method->setAccessible(true);

        // 验证方法成功执行（无异常抛出）
        $result = $method->invoke($this->subscriber, $entity, $reflectionClass);

        // 验证返回值为 null，表示方法正常执行
        $this->assertNull($result, '检查表达式方法应该返回 null');
    }
}
