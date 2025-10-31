<?php

namespace Tourze\EcolBundle\Service;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguageProvider;
use Tourze\EcolBundle\Value\ExpressionValue;

/**
 * 简单的引擎实现
 */
#[Autoconfigure(lazy: true, public: true)]
#[WithMonologChannel(channel: 'ecol')]
class Engine extends ExpressionLanguage
{
    /**
     * @param iterable<\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface> $functionProviders
     * @param iterable<ExpressionValue> $valueProviders
     */
    public function __construct(
        #[AutowireIterator(tag: 'ecol.function.provider')] iterable $functionProviders,
        #[AutowireIterator(tag: 'ecol.value.provider')] private readonly iterable $valueProviders,
        private readonly Security $security,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();

        foreach ($functionProviders as $functionProvider) {
            /** @var \Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface $functionProvider */
            $this->registerProvider($functionProvider);
        }

        // https://symfony.com/doc/current/security/expressions.html 这里的常规方法
        if (class_exists(ExpressionLanguageProvider::class)) {
            $this->registerProvider(new ExpressionLanguageProvider());
        }
    }

    /**
     * @param array<string, mixed> $values
     * @phpstan-ignore-next-line method.childParameterType
     */
    public function evaluate(Expression|string $expression, array $values = []): mixed
    {
        /** @var array<string, mixed> $typedValues */
        $typedValues = $values;
        $values = $this->prepareInitialValues($typedValues);
        $expression = $this->translateChineseOperators(strval($expression));
        $values = $this->processValueProviders($expression, $values);

        return parent::evaluate($expression, $values);
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private function prepareInitialValues(array $values): array
    {
        $values['env'] = $_ENV;
        $values['auth_checker'] = $this->security;
        $values['token'] = $this->security->getToken();

        return $values;
    }

    private function translateChineseOperators(string $expression): string
    {
        $opMapList = [
            '并且' => '&&',
            '并' => '&&',
            '与' => '&&',
            '和' => '&&',
            '或者' => '||',
            '或' => '||',
            '不是' => '!=',
            '不等于' => '!=',
            '等于' => '==',
            '相等于' => '==',
            '是' => '==',
            '全等于' => '===',
            '大于' => '>',
            '多于' => '>',
            '小于' => '<',
            '少于' => '<',
            '大于等于' => '>=',
            '大于或等于' => '>=',
            '等于大于' => '>=',
            '等于或大于' => '>=',
            '小于等于' => '<=',
            '小于或等于' => '<=',
            '等于小于' => '<=',
            '等于或小于' => '<=',
            '加上' => '+',
            '减去' => '-',
            '乘以' => '*',
            '除以' => '/',
        ];

        foreach ($opMapList as $search => $replace) {
            if (str_contains($expression, " {$search} ")) {
                $expression = str_replace(" {$search} ", " {$replace} ", $expression);
            }
        }

        return $expression;
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    private function processValueProviders(string $expression, array $values): array
    {
        foreach ($this->valueProviders as $valueProvider) {
            try {
                /** @var ExpressionValue $valueProvider */
                if ($valueProvider->isSupported($expression, $values)) {
                    foreach ($valueProvider->getNames() as $name) {
                        $values[$name] = $valueProvider->getValue($values);
                    }
                }
            } catch (\Throwable $exception) {
                $this->logger->error('处理额外value时发生错误', [
                    'exception' => $exception,
                    'value' => $valueProvider,
                    'expression' => $expression,
                ]);
            }
        }

        return $values;
    }

    /**
     * 根据表达式内容，再计算一次 values 并返回
     * @param array<string, mixed> $values
     * @param array<string, callable> $valueFunctions
     * @return array<string, mixed>
     */
    public function prepareValues(string $expression, array $values, array $valueFunctions = []): array
    {
        foreach ($valueFunctions as $name => $valueFunction) {
            if (!str_contains($expression, $name)) {
                continue;
            }
            $values[$name] = call_user_func_array($valueFunction, [$values]);
        }

        return $values;
    }
}
