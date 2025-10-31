<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentWeekday1;

/**
 * @internal
 */
#[CoversClass(CurrentWeekday1::class)]
final class CurrentWeekday1Test extends TestCase
{
    private CurrentWeekday1 $currentWeekday1;

    protected function setUp(): void
    {
        parent::setUp();

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
        /** @var CarbonImmutable $date */
        $date = CarbonImmutable::now()->startOfWeek()->weekday(1);
        $expectedDate = $date->format('Ymd');

        $this->assertEquals($expectedDate, $value);
        $this->assertIsString($value);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $value); // 8位数字格式 YYYYMMDD
    }
}
