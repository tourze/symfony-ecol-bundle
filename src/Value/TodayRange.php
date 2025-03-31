<?php

namespace Tourze\EcolBundle\Value;

use Carbon\Carbon;

class TodayRange implements ExpressionValue
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
            '当天日期范围',
        ];
    }

    public function getValue(array $values): mixed
    {
        $date = Carbon::now();

        return [
            $date->clone()->startOfDay()->getTimestamp(),
            $date->clone()->endOfDay()->getTimestamp(),
        ];
    }
}
