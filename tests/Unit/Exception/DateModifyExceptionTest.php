<?php

namespace Tourze\EcolBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tourze\EcolBundle\Exception\DateModifyException;

class DateModifyExceptionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new DateModifyException();
        
        $this->assertInstanceOf(RuntimeException::class, $exception);
        $this->assertInstanceOf(DateModifyException::class, $exception);
    }

    public function testWithMessage(): void
    {
        $message = 'Test date modify exception message';
        $exception = new DateModifyException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testWithMessageAndCode(): void
    {
        $message = 'Test date modify exception message';
        $code = 400;
        $exception = new DateModifyException($message, $code);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function testWithPreviousException(): void
    {
        $previousException = new RuntimeException('Previous exception');
        $exception = new DateModifyException('Date modify exception', 0, $previousException);
        
        $this->assertSame($previousException, $exception->getPrevious());
    }
}