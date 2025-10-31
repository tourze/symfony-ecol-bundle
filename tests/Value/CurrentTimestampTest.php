<?php

namespace Tourze\EcolBundle\Tests\Value;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\CurrentTimestamp;

/**
 * @internal
 */
#[CoversClass(CurrentTimestamp::class)]
final class CurrentTimestampTest extends TestCase
{
    private CurrentTimestamp $currentTimestamp;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentTimestamp = new CurrentTimestamp();
    }

    public function testIsSupportedShouldReturnTrueForMatchingExpression(): void
    {
        $result = $this->currentTimestamp->isSupported('当前时间戳 > 0', []);
        $this->assertTrue($result);
    }

    public function testIsSupportedShouldReturnFalseForNonMatchingExpression(): void
    {
        $result = $this->currentTimestamp->isSupported('不匹配表达式', []);
        $this->assertFalse($result);
    }

    public function testGetNamesShouldReturnExpectedNames(): void
    {
        $names = $this->currentTimestamp->getNames();
        $this->assertContains('当前时间戳', $names);
    }

    public function testGetValueShouldReturnCurrentTimestamp(): void
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
