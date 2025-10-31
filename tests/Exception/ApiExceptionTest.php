<?php

namespace Tourze\EcolBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EcolBundle\Exception\ApiException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(ApiException::class)]
final class ApiExceptionTest extends AbstractExceptionTestCase
{
    protected function onSetUp(): void
    {
        // 这个测试不需要特殊的设置
    }

    public function testInstantiation(): void
    {
        $exception = new ApiException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(ApiException::class, $exception);
    }

    public function testWithMessage(): void
    {
        $message = 'Test API exception message';
        $exception = new ApiException($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    public function testWithMessageAndCode(): void
    {
        $message = 'Test API exception message';
        $code = 500;
        $exception = new ApiException($message, $code);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function testWithPreviousException(): void
    {
        $previousException = new \RuntimeException('Previous exception');
        $exception = new ApiException('API exception', 0, $previousException);

        $this->assertSame($previousException, $exception->getPrevious());
    }
}
