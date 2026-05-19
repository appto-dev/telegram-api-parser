<?php

namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\InputMediaResolver;

class EditMessageMedia
{
    /**
     * for: media (InputMedia)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['media']) || !$properties['media']) {
            return $properties;
        }

        $properties['media'] = (new InputMediaResolver())->resolve($properties['media']);

        return $properties;
    }
}
