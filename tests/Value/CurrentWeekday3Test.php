<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday3;

class CurrentWeekday3Test extends TestCase
{
    private CurrentWeekday3 $currentWeekday3;

    protected function setUp(): void
    {
        $this->currentWeekday3 = new CurrentWeekday3();
    }

    public function testIsSupported(): void
    {
        $this->assertTrue($this->currentWeekday3->isSupported('使用本周周三日期', []));
        $this->assertFalse($this->currentWeekday3->isSupported('使用其他变量', []));
    }

    public function testGetNames(): void
    {
        $names = $this->currentWeekday3->getNames();
        $this->assertCount(1, $names);
        $this->assertContains('本周周三日期', $names);
    }

    public function testGetValue(): void
    {
        $value = $this->currentWeekday3->getValue([]);
        $expectedDate = CarbonImmutable::now()->startOfWeek()->weekday(3)->format('Ymd');

        $this->assertEquals($expectedDate, $value);
        $this->assertIsString($value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
