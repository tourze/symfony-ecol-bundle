<?php

namespace Tourze\EcolBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\EcolBundle\Attribute\Expression;
use Tourze\EcolBundle\Service\Engine;

/**
 * 如果字段内容使用了表达式的话，我们尝试校验一下表达式内容对不对
 */
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class ExpressionCheckSubscriber
{
    public function __construct(
        #[Autowire(service: 'symfony-ecol.property-accessor')] private readonly PropertyAccessor $propertyAccessor,
        private readonly Engine $engine,
    ) {
    }

    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $object = $eventArgs->getObject();
        $this->checkExpression(
            $object,
            $eventArgs->getObjectManager()->getClassMetadata($object::class)->getReflectionClass(),
        );
    }

    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $object = $eventArgs->getObject();
        $this->checkExpression(
            $object,
            $eventArgs->getObjectManager()->getClassMetadata($object::class)->getReflectionClass(),
        );
    }

    private function checkExpression(object $model, ReflectionClass $reflectionClass): void
    {
        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(Expression::class);
            if (empty($attributes)) {
                continue;
            }

            try {
                $val = $this->propertyAccessor->getValue($model, $property->getName());
                if (empty($val)) {
                    continue;
                }
                $this->engine->lint($val, null);
            } catch (SyntaxError $exception) {
                throw new RuntimeException("[{$property->getName()}]语法格式错误：" . $exception->getMessage(), previous: $exception);
            }
        }
    }
}
