<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday2;

class CurrentWeekday2Test extends TestCase
{
    private CurrentWeekday2 $currentWeekday2;

    protected function setUp(): void
    {
        $this->currentWeekday2 = new CurrentWeekday2();
    }

    public function testIsSupported(): void
    {
        $this->assertTrue($this->currentWeekday2->isSupported('使用本周周二日期', []));
        $this->assertFalse($this->currentWeekday2->isSupported('使用其他变量', []));
    }

    public function testGetNames(): void
    {
        $names = $this->currentWeekday2->getNames();
        $this->assertCount(1, $names);
        $this->assertContains('本周周二日期', $names);
    }

    public function testGetValue(): void
    {
        $value = $this->currentWeekday2->getValue([]);
        $expectedDate = CarbonImmutable::now()->startOfWeek()->weekday(2)->format('Ymd');

        $this->assertEquals($expectedDate, $value);
        $this->assertIsString($value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
