<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday6;

class CurrentWeekday6Test extends TestCase
{
    private CurrentWeekday6 $currentWeekday6;

    protected function setUp(): void
    {
        $this->currentWeekday6 = new CurrentWeekday6();
    }

    public function testIsSupported(): void
    {
        $this->assertTrue($this->currentWeekday6->isSupported('使用本周周六日期', []));
        $this->assertFalse($this->currentWeekday6->isSupported('使用其他变量', []));
    }

    public function testGetNames(): void
    {
        $names = $this->currentWeekday6->getNames();
        $this->assertCount(1, $names);
        $this->assertContains('本周周六日期', $names);
    }

    public function testGetValue(): void
    {
        $value = $this->currentWeekday6->getValue([]);
        $expectedDate = CarbonImmutable::now()->startOfWeek()->weekday(6)->format('Ymd');

        $this->assertEquals($expectedDate, $value);
        $this->assertIsString($value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
