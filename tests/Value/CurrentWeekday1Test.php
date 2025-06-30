<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday1;

class CurrentWeekday1Test extends TestCase
{
    private CurrentWeekday1 $currentWeekday1;

    protected function setUp(): void
    {
        $this->currentWeekday1 = new CurrentWeekday1();
    }

    public function testIsSupported(): void
    {
        $this->assertTrue($this->currentWeekday1->isSupported('使用本周周一日期', []));
        $this->assertFalse($this->currentWeekday1->isSupported('使用其他变量', []));
    }

    public function testGetNames(): void
    {
        $names = $this->currentWeekday1->getNames();
        $this->assertCount(1, $names);
        $this->assertContains('本周周一日期', $names);
    }

    public function testGetValue(): void
    {
        $value = $this->currentWeekday1->getValue([]);
        $expectedDate = CarbonImmutable::now()->startOfWeek()->weekday(1)->format('Ymd');

        $this->assertEquals($expectedDate, $value);
        $this->assertIsString($value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
