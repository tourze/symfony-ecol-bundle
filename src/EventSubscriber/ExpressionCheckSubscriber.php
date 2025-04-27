<?php

namespace Tourze\EcolBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\DoctrineHelper\ReflectionHelper;
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

    public function prePersist(PrePersistEventArgs $eventArgs): void
    {
        $this->checkExpression($eventArgs->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $this->checkExpression($eventArgs->getObject());
    }

    private function checkExpression(object $model): void
    {
        $reflection = ReflectionHelper::getClassReflection($model);
        foreach ($reflection->getProperties() as $property) {
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
