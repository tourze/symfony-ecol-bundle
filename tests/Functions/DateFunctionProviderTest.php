<?php

namespace Tourze\EcolBundle\Tests\Functions;

use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tourze\EcolBundle\Functions\DateFunctionProvider;

class DateFunctionProviderTest extends TestCase
{
    private DateFunctionProvider $provider;
    private ExpressionLanguage $expressionLanguage;

    protected function setUp(): void
    {
        $this->provider = new DateFunctionProvider();
        $this->expressionLanguage = new ExpressionLanguage();
        $this->expressionLanguage->registerProvider($this->provider);
    }

    public function testGetFunctions_shouldReturnExpressionFunctionArray(): void
    {
        $functions = $this->provider->getFunctions();
        $this->assertNotEmpty($functions);
        
        foreach ($functions as $function) {
            $this->assertInstanceOf(ExpressionFunction::class, $function);
        }
        
        // 确保包含指定的函数
        $functionNames = array_map(fn($func) => $func->getName(), $functions);
        $this->assertContains('date', $functionNames);
        $this->assertContains('date_modify', $functionNames);
    }

    public function testDateFunction_shouldCreateDateTimeObject(): void
    {
        $result = $this->expressionLanguage->evaluate('date("2023-12-15")');
        
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-12-15', $result->format('Y-m-d'));
    }

    public function testDateModifyFunction_shouldModifyDateTime(): void
    {
        $result = $this->expressionLanguage->evaluate('date_modify(date("2023-12-15"), "+1 day")');
        
        $this->assertInstanceOf(DateTime::class, $result);
        $this->assertEquals('2023-12-16', $result->format('Y-m-d'));
    }

    public function testDateModifyFunction_shouldHandleInvalidDateParam(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('date_modify() expects parameter 1 to be a Date');
        
        $this->expressionLanguage->evaluate('date_modify("not-a-date", "+1 day")');
    }
} 