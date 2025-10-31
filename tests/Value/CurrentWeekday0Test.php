<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday0;

/**
 * @internal
 */
#[CoversClass(CurrentWeekday0::class)]
final class CurrentWeekday0Test extends TestCase
{
    private CurrentWeekday0 $currentWeekday0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentWeekday0 = new CurrentWeekday0();
    }

    public function testIsSupported(): void
    {
        $this->assertTrue($this->currentWeekday0->isSupported('使用本周周日日期', []));
        $this->assertFalse($this->currentWeekday0->isSupported('使用其他变量', []));
    }

    public function testGetNames(): void
    {
        $names = $this->currentWeekday0->getNames();
        $this->assertCount(1, $names);
        $this->assertContains('本周周日日期', $names);
    }

    public function testGetValue(): void
    {
        $value = $this->currentWeekday0->getValue([]);
        /** @var CarbonImmutable $date */
        $date = CarbonImmutable::now()->startOfWeek()->weekday(0);
        $expectedDate = $date->format('Ymd');

        // getValue() 返回的是格式化后的字符串
        $this->assertIsString($value);
        $this->assertEquals($expectedDate, $value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
