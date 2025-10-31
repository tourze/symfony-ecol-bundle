<?php

namespace Tourze\EcolBundle\Functions;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Tourze\EcolBundle\Exception\ApiException;

#[AutoconfigureTag(name: 'ecol.function.provider')]
class ExceptionFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('throwApiException', fn ($message) => sprintf('(throw new ApiException(%s))', is_string($message) ? $message : (is_scalar($message) ? strval($message) : 'Unknown error')), function (array $values, $message): never {
                $messageString = is_string($message) ? $message : (is_scalar($message) ? strval($message) : 'Unknown error');
                throw new ApiException($messageString);
            }),
        ];
    }
}
