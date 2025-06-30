<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday4;

class CurrentWeekday4Test extends TestCase
{
    private CurrentWeekday4 $currentWeekday4;

    protected function setUp(): void
    {
        $this->currentWeekday4 = new CurrentWeekday4();
    }

    public function testIsSupported(): void
    {
        $this->assertTrue($this->currentWeekday4->isSupported('使用本周周四日期', []));
        $this->assertFalse($this->currentWeekday4->isSupported('使用其他变量', []));
    }

    public function testGetNames(): void
    {
        $names = $this->currentWeekday4->getNames();
        $this->assertCount(1, $names);
        $this->assertContains('本周周四日期', $names);
    }

    public function testGetValue(): void
    {
        $value = $this->currentWeekday4->getValue([]);
        $expectedDate = CarbonImmutable::now()->startOfWeek()->weekday(4)->format('Ymd');

        $this->assertEquals($expectedDate, $value);
        $this->assertIsString($value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
