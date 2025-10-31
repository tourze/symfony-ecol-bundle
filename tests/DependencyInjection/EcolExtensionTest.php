<?php

namespace Tourze\EcolBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\EcolBundle\DependencyInjection\EcolExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(EcolExtension::class)]
final class EcolExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}
