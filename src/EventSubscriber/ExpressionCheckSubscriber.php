<?php

namespace Tourze\EcolBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\EcolBundle\Attribute\Expression;
use Tourze\EcolBundle\Exception\ExpressionSyntaxException;
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

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $object = $eventArgs->getObject();
        $this->checkExpression(
            $object,
            $eventArgs->getObjectManager()->getClassMetadata($object::class)->getReflectionClass(),
        );
    }

    /**
     * @param LifecycleEventArgs<EntityManagerInterface> $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $object = $eventArgs->getObject();
        $this->checkExpression(
            $object,
            $eventArgs->getObjectManager()->getClassMetadata($object::class)->getReflectionClass(),
        );
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     */
    private function checkExpression(object $model, \ReflectionClass $reflectionClass): void
    {
        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(Expression::class);
            if ([] === $attributes) {
                continue;
            }

            try {
                $val = $this->propertyAccessor->getValue($model, $property->getName());
                if (null === $val || '' === $val) {
                    continue;
                }
                if (!is_scalar($val)) {
                    continue;
                }
                $this->engine->lint(strval($val), null);
            } catch (SyntaxError $exception) {
                throw new ExpressionSyntaxException("[{$property->getName()}]语法格式错误：" . $exception->getMessage(), previous: $exception);
            }
        }
    }
}
