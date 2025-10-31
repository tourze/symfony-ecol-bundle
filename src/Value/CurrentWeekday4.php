<?php

namespace Tourze\EcolBundle\Value;

use Carbon\CarbonImmutable;

class CurrentWeekday4 implements ExpressionValue
{
    /** @param array<string, mixed> $values */
    public function isSupported(string $expression, array $values): bool
    {
        foreach ($this->getNames() as $name) {
            if (str_contains($expression, $name)) {
                return true;
            }
        }

        return false;
    }

    /** @return array<string> */
    public function getNames(): array
    {
        return [
            '本周周四日期',
        ];
    }

    /** @param array<string, mixed> $values */
    public function getValue(array $values): mixed
    {
        /** @var CarbonImmutable $date */
        $date = CarbonImmutable::now()->startOfWeek()->weekday(4);

        return $date->format('Ymd');
    }
}
