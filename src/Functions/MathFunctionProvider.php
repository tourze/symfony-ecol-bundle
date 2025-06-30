<?php

namespace Tourze\EcolBundle\Functions;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * 数学相关函数
 */
#[AutoconfigureTag(name: 'ecol.function.provider')]
class MathFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('取绝对值', fn ($number) => sprintf('取绝对值(%s)', $number), function (array $values, $number): mixed {
                return abs($number);
            }),
            new ExpressionFunction('取负数', fn ($number) => sprintf('取负数(%s)', $number), function (array $values, $number): mixed {
                return -abs($number);
            }),
            new ExpressionFunction('加上', fn ($number1, $number2) => sprintf('加上(%s, %s)', $number1, $number2), function (array $values, $number1, $number2): mixed {
                return floatval($number1) + floatval($number2);
            }),
        ];
    }
}
