<?php

namespace Tourze\EcolBundle\Tests\Value;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\Value\Today;

/**
 * @internal
 */
#[CoversClass(Today::class)]
final class TodayTest extends TestCase
{
    private Today $today;

    protected function setUp(): void
    {
        parent::setUp();

        $this->today = new Today();
    }

    public function testIsSupportedShouldReturnTrueForMatchingExpression(): void
    {
        $result = $this->today->isSupported('当天日期 == "20231215"', []);
        $this->assertTrue($result);
    }

    public function testIsSupportedShouldReturnFalseForNonMatchingExpression(): void
    {
        $result = $this->today->isSupported('不匹配表达式', []);
        $this->assertFalse($result);
    }

    public function testGetNamesShouldReturnExpectedNames(): void
    {
        $names = $this->today->getNames();
        $this->assertContains('当天日期', $names);
    }

    public function testGetValueShouldReturnCurrentDateInCorrectFormat(): void
    {
        $expectedFormat = CarbonImmutable::now()->format('Ymd');
        $value = $this->today->getValue([]);
        $this->assertEquals($expectedFormat, $value);
    }
}
