<?php

namespace Tourze\EcolBundle\Value;

use Carbon\CarbonImmutable;

class CurrentWeekday3 implements ExpressionValue
{
    public function isSupported(string $expression, array $values): bool
    {
        foreach ($this->getNames() as $name) {
            if (str_contains($expression, $name)) {
                return true;
            }
        }

        return false;
    }

    public function getNames(): array
    {
        return [
            '本周周三日期',
        ];
    }

    public function getValue(array $values): mixed
    {
        return CarbonImmutable::now()->startOfWeek()->weekday(3)->format('Ymd');
    }
}
