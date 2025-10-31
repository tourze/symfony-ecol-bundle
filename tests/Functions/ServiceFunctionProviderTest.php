<?php

namespace Tourze\EcolBundle\Tests\Functions;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tourze\EcolBundle\Functions\ServiceFunctionProvider;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ServiceFunctionProvider::class)]
#[RunTestsInSeparateProcesses]
final class ServiceFunctionProviderTest extends AbstractIntegrationTestCase
{
    private ExpressionLanguage $expressionLanguage;

    protected function onSetUp(): void
    {
        $provider = self::getService(ServiceFunctionProvider::class);
        $this->expressionLanguage = new ExpressionLanguage();
        $this->expressionLanguage->registerProvider($provider);
    }

    public function testEnvFunction(): void
    {
        $_ENV['TEST_VAR'] = 'test_value';

        $result = $this->expressionLanguage->evaluate('env("TEST_VAR")');
        $this->assertEquals('test_value', $result);

        unset($_ENV['TEST_VAR']);
    }

    public function testEnvFunctionWithMissingVar(): void
    {
        $result = $this->expressionLanguage->evaluate('env("NON_EXISTENT_VAR")');
        $this->assertEquals('', $result);
    }

    public function testHasEnvFunction(): void
    {
        $_ENV['TEST_VAR'] = 'test_value';

        $result = $this->expressionLanguage->evaluate('hasEnv("TEST_VAR")');
        $this->assertTrue($result);

        unset($_ENV['TEST_VAR']);

        $result = $this->expressionLanguage->evaluate('hasEnv("TEST_VAR")');
        $this->assertFalse($result);
    }

    public function testGetFunctions(): void
    {
        $provider = self::getService(ServiceFunctionProvider::class);
        $functions = $provider->getFunctions();

        $this->assertCount(2, $functions);
        $functionNames = array_map(fn ($f) => $f->getName(), $functions);
        $this->assertContains('env', $functionNames);
        $this->assertContains('hasEnv', $functionNames);
    }
}
