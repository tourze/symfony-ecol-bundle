<?php

namespace Tourze\EcolBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\EcolBundle\DependencyInjection\EcolExtension;

class EcolExtensionTest extends TestCase
{
    private EcolExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new EcolExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad_shouldLoadServices(): void
    {
        $this->extension->load([], $this->container);
        
        // 验证PropertyAccessor服务定义
        $this->assertTrue($this->container->hasDefinition('symfony-ecol.property-accessor'));
        
        // 验证目录资源导入
        $servicePattern = $this->container->getDefinition('symfony-ecol.property-accessor')->getClass();
        $this->assertEquals('Symfony\Component\PropertyAccess\PropertyAccessor', $servicePattern);
    }
} 