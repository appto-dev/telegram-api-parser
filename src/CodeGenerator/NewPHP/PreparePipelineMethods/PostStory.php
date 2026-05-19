<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\InputStoryContentResolver;

class PostStory
{
    /**
     * for: content (InputStoryContent)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['content']) || !$properties['content']) {
            return $properties;
        }

        $properties['content'] = (new InputStoryContentResolver())->resolve($properties['content']);

        return $properties;
    }
}
