<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\StoryAreaTypeResolver;

class StoryArea
{
    /**
     * for: type (StoryAreaType)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['type']) || !$properties['type']) {
            return $properties;
        }

        $properties['type'] = (new StoryAreaTypeResolver())->resolve($properties['type']);

        return $properties;
    }
}
