<?php

namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\MaybeInaccessibleMessageResolver;

class CallbackQuery
{
    public function __invoke(array $properties): array
    {
        if (!isset($properties['message']) || !$properties['message']) {
            return $properties;
        }

        $properties['message'] = (new MaybeInaccessibleMessageResolver())
            ->resolve($properties['message']);

        return $properties;
    }
}
