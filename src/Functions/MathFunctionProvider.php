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
            new ExpressionFunction('取绝对值', fn ($number) => sprintf('取绝对值(%s)', $this->formatNumber($number)), function (array $values, $number): float|int {
                return abs($this->toNumeric($number));
            }),
            new ExpressionFunction('取负数', fn ($number) => sprintf('取负数(%s)', $this->formatNumber($number)), function (array $values, $number): float|int {
                return -abs($this->toNumeric($number));
            }),
            new ExpressionFunction('加上', fn ($number1, $number2) => sprintf('加上(%s, %s)', $this->formatNumber($number1), $this->formatNumber($number2)), function (array $values, $number1, $number2): float {
                return $this->toFloat($number1) + $this->toFloat($number2);
            }),
        ];
    }

    private function formatNumber(mixed $number): string
    {
        return is_numeric($number) ? strval($number) : '0';
    }

    private function toNumeric(mixed $number): float|int
    {
        if (!is_numeric($number)) {
            return 0;
        }
        return is_float($number) ? $number : (is_int($number) ? $number : floatval($number));
    }

    private function toFloat(mixed $number): float
    {
        return is_numeric($number) ? floatval($number) : 0.0;
    }
}
