<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\BackgroundTypeResolve;

class ChatBackground
{
    /**
     * for: type (BackgroundType)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['type']) || !$properties['type']) {
            return $properties;
        }

        $properties['type'] = (new BackgroundTypeResolve())->resolve($properties['type']);

        return $properties;
    }
}
