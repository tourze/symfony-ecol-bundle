<?php

namespace Tourze\EcolBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguageProvider;
use Tourze\EcolBundle\Value\ExpressionValue;

/**
 * 简单的引擎实现
 */
#[Autoconfigure(lazy: true, public: true)]
class Engine extends ExpressionLanguage
{
    public function __construct(
        #[TaggedIterator('ecol.function.provider')] iterable $functionProviders,
        #[TaggedIterator('ecol.value.provider')] private readonly iterable $valueProviders,
        private readonly Security $security,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();

        foreach ($functionProviders as $functionProvider) {
            $this->registerProvider($functionProvider);
        }

        // https://symfony.com/doc/current/security/expressions.html 这里的常规方法
        if (class_exists(ExpressionLanguageProvider::class)) {
            $this->registerProvider(new ExpressionLanguageProvider());
        }
    }

    public function evaluate(Expression|string $expression, array $values = []): mixed
    {
        // 环境变量直接合并进去
        $values['env'] = $_ENV;

        // 兼容 Security 带来的函数
        $values['auth_checker'] = $this->security;
        $values['token'] = $this->security->getToken();

        // 如果表达式中带有一些特定字符，我们就自动做一次替换判断
        // 通过这种方式，我们可以减少一些模板代码。
        // 同时，这里也是为了兼容旧 yii2 ruler 那套规则。
        $expression = strval($expression);

        // 在 Yii2 版本的 ruler 中，我们实现了一套中文的语法解析，这里我们暂时粗暴点替换了先，后面慢慢修正
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

        foreach ($this->valueProviders as $valueProvider) {
            //            $this->logger->debug('开始判断value：' . get_class($valueProvider), [
            //                'provider' => $valueProvider,
            //            ]);

            try {
                /** @var ExpressionValue $valueProvider */
                if ($valueProvider->isSupported($expression, $values)) {
                    foreach ($valueProvider->getNames() as $name) {
                        //                    if (isset($values[$name])) {
                        //                        continue;
                        //                    }
                        // 为了简化，我们暂时支持覆盖算了
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

        return parent::evaluate($expression, $values);
    }

    /**
     * 根据表达式内容，再计算一次 values 并返回
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
