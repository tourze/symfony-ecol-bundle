<?php

namespace Tourze\EcolBundle\Value;

use Carbon\CarbonImmutable;

class CurrentYear implements ExpressionValue
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
            '当前年份',
        ];
    }

    public function getValue(array $values): mixed
    {
        return CarbonImmutable::now()->year;
    }
}
