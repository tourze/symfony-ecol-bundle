<?php

namespace Tourze\EcolBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\EcolBundle\Attribute\Expression;
use Tourze\EcolBundle\EventSubscriber\ExpressionCheckSubscriber;
use Tourze\EcolBundle\Service\Engine;

class ExpressionCheckSubscriberTest extends TestCase
{
    private ExpressionCheckSubscriber $subscriber;
    private MockObject $propertyAccessor;
    private MockObject $engine;

    protected function setUp(): void
    {
        $this->propertyAccessor = $this->createMock(PropertyAccessor::class);
        $this->engine = $this->createMock(Engine::class);
        
        $this->subscriber = new ExpressionCheckSubscriber(
            $this->propertyAccessor,
            $this->engine
        );
    }

    public function testPrePersist_shouldValidateExpressions(): void
    {
        // 创建测试实体
        $entity = new class {
            #[Expression]
            /** @phpstan-ignore-next-line */
            private string $condition = 'a > b';
            
            #[Expression]
            /** @phpstan-ignore-next-line */
            private string $emptyCondition = '';
            
            /** @phpstan-ignore-next-line */
            private string $nonExpressionField = 'some value';
        };
        
        // 模拟ReflectionClass
        $reflectionClass = new ReflectionClass($entity);
        
        // 模拟EntityManager和ClassMetadata
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        
        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);
            
        $classMetadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflectionClass);
        
        // 使用LifecycleEventArgs替代PrePersistEventArgs（因为后者是final无法mock）
        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->expects($this->once())
            ->method('getObject')
            ->willReturn($entity);
            
        $eventArgs->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($objectManager);
        
        // 模拟PropertyAccessor行为
        $this->propertyAccessor->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnMap([
                [$entity, 'condition', 'a > b'],
                [$entity, 'emptyCondition', ''],
            ]);
        
        // 模拟Engine行为，只验证非空表达式
        $this->engine->expects($this->once())
            ->method('lint')
            ->with('a > b');
        
        // 执行测试
        $this->subscriber->prePersist($eventArgs);
    }

    public function testPreUpdate_shouldValidateExpressions(): void
    {
        // 创建测试实体
        $entity = new class {
            #[Expression]
            /** @phpstan-ignore-next-line */
            private string $condition = 'a > b';
        };
        
        // 模拟ReflectionClass
        $reflectionClass = new ReflectionClass($entity);
        
        // 模拟EntityManager和ClassMetadata
        $objectManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        
        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->willReturn($classMetadata);
            
        $classMetadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflectionClass);
        
        // 使用LifecycleEventArgs替代PreUpdateEventArgs（因为后者是final无法mock）
        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->expects($this->once())
            ->method('getObject')
            ->willReturn($entity);
            
        $eventArgs->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($objectManager);
        
        // 模拟PropertyAccessor行为
        $this->propertyAccessor->expects($this->once())
            ->method('getValue')
            ->with($entity, 'condition')
            ->willReturn('a > b');
        
        // 模拟Engine行为
        $this->engine->expects($this->once())
            ->method('lint')
            ->with('a > b');
        
        // 执行测试
        $this->subscriber->preUpdate($eventArgs);
    }

    public function testCheckExpression_shouldThrowExceptionForInvalidExpression(): void
    {
        // 创建测试实体
        $entity = new class {
            #[Expression]
            /** @phpstan-ignore-next-line */
            private string $invalidCondition = 'a > >';
        };
        
        // 模拟ReflectionClass
        $reflectionClass = new ReflectionClass($entity);
        
        // 模拟PropertyAccessor行为
        $this->propertyAccessor->expects($this->once())
            ->method('getValue')
            ->with($entity, 'invalidCondition')
            ->willReturn('a > >');
        
        // 模拟Engine抛出语法错误
        $syntaxError = new SyntaxError('Unexpected token ">" around position 4.', 4, 'a > >');
        $this->engine->expects($this->once())
            ->method('lint')
            ->with('a > >')
            ->willThrowException($syntaxError);
        
        // 期望抛出RuntimeException，包含属性名和原始异常信息
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("[invalidCondition]语法格式错误：");
        
        // 通过反射调用私有方法
        $method = new \ReflectionMethod(ExpressionCheckSubscriber::class, 'checkExpression');
        $method->setAccessible(true);
        $method->invoke($this->subscriber, $entity, $reflectionClass);
    }

    public function testCheckExpression_shouldSkipEmptyValues(): void
    {
        // 创建测试实体
        $entity = new class {
            #[Expression]
            /** @phpstan-ignore-next-line */
            private string $emptyCondition = '';
        };
        
        // 模拟ReflectionClass
        $reflectionClass = new ReflectionClass($entity);
        
        // 模拟PropertyAccessor行为
        $this->propertyAccessor->expects($this->once())
            ->method('getValue')
            ->with($entity, 'emptyCondition')
            ->willReturn('');
        
        // Engine不应被调用
        $this->engine->expects($this->never())
            ->method('lint');
        
        // 通过反射调用私有方法
        $method = new \ReflectionMethod(ExpressionCheckSubscriber::class, 'checkExpression');
        $method->setAccessible(true);
        $method->invoke($this->subscriber, $entity, $reflectionClass);
        
        // 如果没有异常抛出，测试通过
        $this->assertTrue(true);
    }
} 