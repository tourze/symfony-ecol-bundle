<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\TodayRange;

class TodayRangeTest extends TestCase
{
    private TodayRange $todayRange;

    protected function setUp(): void
    {
        $this->todayRange = new TodayRange();
    }

    public function testIsSupported_shouldReturnTrueForMatchingExpression(): void
    {
        $result = $this->todayRange->isSupported('当天日期范围 > 0', []);
        $this->assertTrue($result);
    }

    public function testIsSupported_shouldReturnFalseForNonMatchingExpression(): void
    {
        $result = $this->todayRange->isSupported('不匹配表达式', []);
        $this->assertFalse($result);
    }

    public function testGetNames_shouldReturnExpectedNames(): void
    {
        $names = $this->todayRange->getNames();
        $this->assertContains('当天日期范围', $names);
    }

    public function testGetValue_shouldReturnTodayStartAndEndTimestamps(): void
    {
        // 设置固定的日期时间以便测试
        $testDate = CarbonImmutable::create(2023, 12, 15, 12, 30, 45);
        CarbonImmutable::setTestNow($testDate);
        
        try {
            $expectedStart = CarbonImmutable::today()->timestamp;
            $expectedEnd = CarbonImmutable::today()->endOfDay()->timestamp;
            
            $value = $this->todayRange->getValue([]);
            $this->assertCount(2, $value);
            $this->assertEquals($expectedStart, $value[0]);
            $this->assertEquals($expectedEnd, $value[1]);
            
            // 确保开始时间戳小于结束时间戳
            $this->assertLessThan($value[1], $value[0]);
            
            // 确保日期范围是24小时
            $this->assertEquals(86399, $value[1] - $value[0]);
        } finally {
            // 重置测试时间
            CarbonImmutable::setTestNow(null);
        }
    }
} 