<?php

namespace Tourze\EcolBundle\Functions;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * 常用的日期相关函数
 *
 * @see https://voutzinos.com/blog/using-symfony2-expression-language/
 */
#[AutoconfigureTag('ecol.function.provider')]
class DateFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('date', fn ($date) => sprintf('(new \DateTime(%s))', $date), fn (array $values, $date) => new \DateTime($date)),

            new ExpressionFunction('date_modify', fn ($date, $modify) => sprintf('%s->modify(%s)', $date, $modify), function (array $values, $date, $modify): \DateTime|bool {
                if (!$date instanceof \DateTime) {
                    throw new \RuntimeException('date_modify() expects parameter 1 to be a Date');
                }

                return $date->modify($modify);
            }),
        ];
    }
}
