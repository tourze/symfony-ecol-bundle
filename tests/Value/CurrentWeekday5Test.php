<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday5;

class CurrentWeekday5Test extends TestCase
{
    private CurrentWeekday5 $currentWeekday5;

    protected function setUp(): void
    {
        $this->currentWeekday5 = new CurrentWeekday5();
    }

    public function testIsSupported(): void
    {
        $this->assertTrue($this->currentWeekday5->isSupported('使用本周周五日期', []));
        $this->assertFalse($this->currentWeekday5->isSupported('使用其他变量', []));
    }

    public function testGetNames(): void
    {
        $names = $this->currentWeekday5->getNames();
        $this->assertCount(1, $names);
        $this->assertContains('本周周五日期', $names);
    }

    public function testGetValue(): void
    {
        $value = $this->currentWeekday5->getValue([]);
        $expectedDate = CarbonImmutable::now()->startOfWeek()->weekday(5)->format('Ymd');

        $this->assertEquals($expectedDate, $value);
        $this->assertIsString($value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
