<?php

namespace Tourze\EcolBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class EcolExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
