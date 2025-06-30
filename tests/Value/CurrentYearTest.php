<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentYear;

class CurrentYearTest extends TestCase
{
    private CurrentYear $currentYear;

    protected function setUp(): void
    {
        $this->currentYear = new CurrentYear();
    }

    public function testIsSupported(): void
    {
        $this->assertTrue($this->currentYear->isSupported('使用当前年份', []));
        $this->assertFalse($this->currentYear->isSupported('使用其他变量', []));
    }

    public function testGetNames(): void
    {
        $names = $this->currentYear->getNames();
        $this->assertCount(1, $names);
        $this->assertContains('当前年份', $names);
    }

    public function testGetValue(): void
    {
        $value = $this->currentYear->getValue([]);
        $expectedYear = CarbonImmutable::now()->year;

        $this->assertEquals($expectedYear, $value);
        $this->assertIsInt($value);
        $this->assertGreaterThan(2000, $value); // 假设测试在2000年后运行
    }
}
