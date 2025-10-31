<?php

namespace Tourze\EcolBundle\Tests\Functions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tourze\EcolBundle\Exception\ApiException;
use Tourze\EcolBundle\Functions\ExceptionFunctionProvider;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ExceptionFunctionProvider::class)]
#[RunTestsInSeparateProcesses]
final class ExceptionFunctionProviderTest extends AbstractIntegrationTestCase
{
    private ExpressionLanguage $expressionLanguage;

    protected function onSetUp(): void
    {
        $provider = self::getService(ExceptionFunctionProvider::class);
        $this->expressionLanguage = new ExpressionLanguage();
        $this->expressionLanguage->registerProvider($provider);
    }

    public function testThrowApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('测试错误消息');

        $this->expressionLanguage->evaluate('throwApiException("测试错误消息")');
    }

    public function testGetFunctions(): void
    {
        $provider = self::getService(ExceptionFunctionProvider::class);
        $functions = $provider->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertEquals('throwApiException', $functions[0]->getName());
    }
}
