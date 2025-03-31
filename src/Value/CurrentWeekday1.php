<?php

namespace Tourze\EcolBundle\Value;

use Carbon\Carbon;

class CurrentWeekday1 implements ExpressionValue
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
            '本周周一日期',
        ];
    }

    public function getValue(array $values): mixed
    {
        return Carbon::now()->startOfWeek()->weekday(1)->format('Ymd');
    }
}
