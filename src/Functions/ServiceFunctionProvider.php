<?php

namespace Tourze\EcolBundle\Functions;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * 参考 \Symfony\Component\DependencyInjection\ExpressionLanguageProvider 新增一些服务函数
 */
#[AutoconfigureTag('ecol.function.provider')]
class ServiceFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('env', fn ($key) => sprintf('env(%s)', $key), fn (array $values, $key) => $_ENV[$key] ?? ''),
            new ExpressionFunction('hasEnv', fn ($key) => sprintf('hasEnv(%s)', $key), fn (array $values, $key) => isset($_ENV[$key])),
        ];
    }
}
