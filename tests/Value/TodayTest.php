<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\Today;

class TodayTest extends TestCase
{
    private Today $today;

    protected function setUp(): void
    {
        $this->today = new Today();
    }

    public function testIsSupported_shouldReturnTrueForMatchingExpression(): void
    {
        $result = $this->today->isSupported('当天日期 == "20231215"', []);
        $this->assertTrue($result);
    }

    public function testIsSupported_shouldReturnFalseForNonMatchingExpression(): void
    {
        $result = $this->today->isSupported('不匹配表达式', []);
        $this->assertFalse($result);
    }

    public function testGetNames_shouldReturnExpectedNames(): void
    {
        $names = $this->today->getNames();
        $this->assertIsArray($names);
        $this->assertContains('当天日期', $names);
    }

    public function testGetValue_shouldReturnCurrentDateInCorrectFormat(): void
    {
        $expectedFormat = Carbon::now()->format('Ymd');
        $value = $this->today->getValue([]);
        $this->assertEquals($expectedFormat, $value);
    }
} 