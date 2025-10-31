<?php

namespace Tourze\EcolBundle\Functions;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Tourze\EcolBundle\Exception\DateModifyException;

/**
 * 常用的日期相关函数
 *
 * @see https://voutzinos.com/blog/using-symfony2-expression-language/
 */
#[AutoconfigureTag(name: 'ecol.function.provider')]
class DateFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('date', fn ($date) => sprintf('(new \DateTime(%s))', $this->formatDateParam($date)), function (array $values, $date): \DateTime {
                return new \DateTime($this->formatDateParam($date));
            }),

            new ExpressionFunction('date_modify', fn ($date, $modify) => sprintf('%s->modify(%s)', $this->formatDateParam($date), $this->formatModifyParam($modify)), function (array $values, $date, $modify): \DateTime|bool {
                if (!$date instanceof \DateTime) {
                    throw new DateModifyException('date_modify() expects parameter 1 to be a Date');
                }

                return $date->modify($this->formatModifyParam($modify));
            }),
        ];
    }

    private function formatDateParam(mixed $date): string
    {
        return is_string($date) ? $date : (is_scalar($date) ? strval($date) : 'now');
    }

    private function formatModifyParam(mixed $modify): string
    {
        return is_string($modify) ? $modify : (is_scalar($modify) ? strval($modify) : '');
    }
}
