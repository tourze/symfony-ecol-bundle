<?php

namespace Tourze\EcolBundle\Value;

use Carbon\CarbonImmutable;

class CurrentWeekday4 implements ExpressionValue
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
            '本周周四日期',
        ];
    }

    public function getValue(array $values): mixed
    {
        return CarbonImmutable::now()->startOfWeek()->weekday(4)->format('Ymd');
    }
}
