<?php

declare(strict_types=1);

namespace Tourze\EcolBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EcolBundle\EcolBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(EcolBundle::class)]
#[RunTestsInSeparateProcesses]
final class EcolBundleTest extends AbstractBundleTestCase
{
}
