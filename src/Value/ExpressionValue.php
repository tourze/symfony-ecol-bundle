<?php

namespace Tourze\EcolBundle\Value;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * 变量值
 */
#[AutoconfigureTag(name: 'ecol.value.provider')]
interface ExpressionValue
{
    /**
     * 判断这个变量是否支持该表达式
     *
     * @param array<string, mixed> $values 原始的数值列表
     */
    public function isSupported(string $expression, array $values): bool;

    /**
     * @return array<string> 变量名，同一个值可能有多个名的
     */
    public function getNames(): array;

    /**
     * @param array<string, mixed> $values 原始的数值列表
     *
     * @return mixed 最终值
     */
    public function getValue(array $values): mixed;
}
