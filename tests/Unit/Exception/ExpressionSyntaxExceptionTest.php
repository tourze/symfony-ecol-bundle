<?php

namespace Tourze\EcolBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tourze\EcolBundle\Exception\ExpressionSyntaxException;

class ExpressionSyntaxExceptionTest extends TestCase
{
    public function testInstantiation(): void
    {
        $exception = new ExpressionSyntaxException();
        
        $this->assertInstanceOf(RuntimeException::class, $exception);
        $this->assertInstanceOf(ExpressionSyntaxException::class, $exception);
    }

    public function testWithMessage(): void
    {
        $message = 'Test expression syntax exception message';
        $exception = new ExpressionSyntaxException($message);
        
        $this->assertEquals($message, $exception->getMessage());
    }

    public function testWithMessageAndCode(): void
    {
        $message = 'Test expression syntax exception message';
        $code = 422;
        $exception = new ExpressionSyntaxException($message, $code);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }

    public function testWithPreviousException(): void
    {
        $previousException = new RuntimeException('Previous exception');
        $exception = new ExpressionSyntaxException('Syntax exception', 0, $previousException);
        
        $this->assertSame($previousException, $exception->getPrevious());
    }
}