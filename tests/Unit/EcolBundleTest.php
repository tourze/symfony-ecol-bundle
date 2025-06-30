<?php

namespace Tourze\EcolBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tourze\EcolBundle\EcolBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EcolBundleTest extends TestCase
{
    private EcolBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new EcolBundle();
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
        $this->assertInstanceOf(EcolBundle::class, $this->bundle);
    }

    public function testGetName(): void
    {
        $this->assertEquals('EcolBundle', $this->bundle->getName());
    }
}