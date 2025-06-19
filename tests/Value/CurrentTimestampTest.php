<?php

namespace Tourze\EcolBundle\Tests\Value;

use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentTimestamp;

class CurrentTimestampTest extends TestCase
{
    private CurrentTimestamp $currentTimestamp;

    protected function setUp(): void
    {
        $this->currentTimestamp = new CurrentTimestamp();
    }

    public function testIsSupported_shouldReturnTrueForMatchingExpression(): void
    {
        $result = $this->currentTimestamp->isSupported('当前时间戳 > 0', []);
        $this->assertTrue($result);
    }

    public function testIsSupported_shouldReturnFalseForNonMatchingExpression(): void
    {
        $result = $this->currentTimestamp->isSupported('不匹配表达式', []);
        $this->assertFalse($result);
    }

    public function testGetNames_shouldReturnExpectedNames(): void
    {
        $names = $this->currentTimestamp->getNames();
        $this->assertContains('当前时间戳', $names);
    }

    public function testGetValue_shouldReturnCurrentTimestamp(): void
    {
        $before = time();
        $value = $this->currentTimestamp->getValue([]);
        $after = time();
        
        // 确保返回的时间戳在调用前后的时间范围内
        $this->assertGreaterThanOrEqual($before, $value);
        $this->assertLessThanOrEqual($after, $value);
        $this->assertIsInt($value);
    }
} 