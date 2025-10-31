<?php

namespace Tourze\EcolBundle\Functions;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * 参考 \Symfony\Component\DependencyInjection\ExpressionLanguageProvider 新增一些服务函数
 */
#[AutoconfigureTag(name: 'ecol.function.provider')]
class ServiceFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('env', fn ($key) => sprintf('env(%s)', $this->formatKey($key)), function (array $values, $key): string {
                $keyString = $this->formatKey($key);
                $envValue = $_ENV[$keyString] ?? '';
                return $this->formatEnvValue($envValue);
            }),
            new ExpressionFunction('hasEnv', fn ($key) => sprintf('hasEnv(%s)', $this->formatKey($key)), function (array $values, $key): bool {
                return isset($_ENV[$this->formatKey($key)]);
            }),
        ];
    }

    private function formatKey(mixed $key): string
    {
        return is_string($key) ? $key : (is_scalar($key) ? strval($key) : 'undefined');
    }

    private function formatEnvValue(mixed $envValue): string
    {
        return is_string($envValue) ? $envValue : (is_scalar($envValue) ? strval($envValue) : '');
    }
}
