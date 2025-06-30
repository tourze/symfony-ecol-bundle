<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentMonth;

class CurrentMonthTest extends TestCase
{
    private CurrentMonth $currentMonth;

    protected function setUp(): void
    {
        $this->currentMonth = new CurrentMonth();
    }

    public function testIsSupported(): void
    {
        $this->assertTrue($this->currentMonth->isSupported('使用当前月份', []));
        $this->assertFalse($this->currentMonth->isSupported('使用其他变量', []));
    }

    public function testGetNames(): void
    {
        $names = $this->currentMonth->getNames();
        $this->assertCount(1, $names);
        $this->assertContains('当前月份', $names);
    }

    public function testGetValue(): void
    {
        $value = $this->currentMonth->getValue([]);
        $expectedMonth = CarbonImmutable::now()->month;

        $this->assertEquals($expectedMonth, $value);
        $this->assertIsInt($value);
        $this->assertGreaterThanOrEqual(1, $value);
        $this->assertLessThanOrEqual(12, $value);
    }
}
