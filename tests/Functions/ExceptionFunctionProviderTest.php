<?php

namespace Tourze\EcolBundle\Tests\Functions;

use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tourze\EcolBundle\Exception\ApiException;
use Tourze\EcolBundle\Functions\ExceptionFunctionProvider;

class ExceptionFunctionProviderTest extends TestCase
{
    private ExpressionLanguage $expressionLanguage;

    protected function setUp(): void
    {
        $this->expressionLanguage = new ExpressionLanguage();
        $this->expressionLanguage->registerProvider(new ExceptionFunctionProvider());
    }

    public function testThrowApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('测试错误消息');

        $this->expressionLanguage->evaluate('throwApiException("测试错误消息")');
    }

    public function testGetFunctions(): void
    {
        $provider = new ExceptionFunctionProvider();
        $functions = $provider->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertEquals('throwApiException', $functions[0]->getName());
    }
}
