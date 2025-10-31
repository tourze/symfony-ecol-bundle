<?php

namespace Tourze\EcolBundle\Tests\Functions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tourze\EcolBundle\Exception\DateModifyException;
use Tourze\EcolBundle\Functions\DateFunctionProvider;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DateFunctionProvider::class)]
#[RunTestsInSeparateProcesses]
final class DateFunctionProviderTest extends AbstractIntegrationTestCase
{
    private DateFunctionProvider $provider;

    private ExpressionLanguage $expressionLanguage;

    protected function onSetUp(): void
    {
        $this->provider = self::getService(DateFunctionProvider::class);
        $this->expressionLanguage = new ExpressionLanguage();
        $this->expressionLanguage->registerProvider($this->provider);
    }

    public function testGetFunctionsShouldReturnExpressionFunctionArray(): void
    {
        $functions = $this->provider->getFunctions();
        $this->assertNotEmpty($functions);

        foreach ($functions as $function) {
            $this->assertInstanceOf(ExpressionFunction::class, $function);
        }

        // 确保包含指定的函数
        $functionNames = array_map(fn ($func) => $func->getName(), $functions);
        $this->assertContains('date', $functionNames);
        $this->assertContains('date_modify', $functionNames);
    }

    public function testDateFunctionShouldCreateDateTimeObject(): void
    {
        $result = $this->expressionLanguage->evaluate('date("2023-12-15")');

        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertEquals('2023-12-15', $result->format('Y-m-d'));
    }

    public function testDateModifyFunctionShouldModifyDateTime(): void
    {
        $result = $this->expressionLanguage->evaluate('date_modify(date("2023-12-15"), "+1 day")');

        $this->assertInstanceOf(\DateTime::class, $result);
        $this->assertEquals('2023-12-16', $result->format('Y-m-d'));
    }

    public function testDateModifyFunctionShouldHandleInvalidDateParam(): void
    {
        $this->expectException(DateModifyException::class);
        $this->expectExceptionMessage('date_modify() expects parameter 1 to be a Date');

        $this->expressionLanguage->evaluate('date_modify("not-a-date", "+1 day")');
    }
}
