<?php

namespace Tourze\EcolBundle\Functions;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

#[AutoconfigureTag('ecol.function.provider')]
class ExceptionFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('throwApiException', fn ($message) => sprintf('(throw new ApiException(%s))', $message), function (array $values, $message): never {
                throw new \RuntimeException($message);
            }),
        ];
    }
}
