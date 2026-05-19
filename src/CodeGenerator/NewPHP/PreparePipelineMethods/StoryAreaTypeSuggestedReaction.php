<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\ReactionTypeResolve;

class StoryAreaTypeSuggestedReaction
{
    /**
     * for: reaction_type (ReactionType)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['reaction_type']) || !$properties['reaction_type']) {
            return $properties;
        }

        $properties['reaction_type'] = (new ReactionTypeResolve())
            ->resolveCollection($properties['reaction_type']);

        return $properties;
    }
}
