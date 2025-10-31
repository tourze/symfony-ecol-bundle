<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday6;

/**
 * @internal
 */
#[CoversClass(CurrentWeekday6::class)]
final class CurrentWeekday6Test extends TestCase
{
    private CurrentWeekday6 $currentWeekday6;

    protected function setUp(): void
    {
        parent::setUp();

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
        /** @var CarbonImmutable $date */
        $date = CarbonImmutable::now()->startOfWeek()->weekday(6);
        $expectedDate = $date->format('Ymd');

        $this->assertEquals($expectedDate, $value);
        $this->assertIsString($value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
