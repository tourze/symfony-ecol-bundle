<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday4;

/**
 * @internal
 */
#[CoversClass(CurrentWeekday4::class)]
final class CurrentWeekday4Test extends TestCase
{
    private CurrentWeekday4 $currentWeekday4;

    protected function setUp(): void
    {
        parent::setUp();

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
        /** @var CarbonImmutable $date */
        $date = CarbonImmutable::now()->startOfWeek()->weekday(4);
        $expectedDate = $date->format('Ymd');

        $this->assertEquals($expectedDate, $value);
        $this->assertIsString($value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
