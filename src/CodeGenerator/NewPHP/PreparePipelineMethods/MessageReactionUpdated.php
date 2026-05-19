<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\ReactionTypeResolve;

class MessageReactionUpdated
{
    /**
     * for: old_reaction (ReactionType)
     */
    public function __invoke(array $properties): array
    {
        if (isset($properties['old_reaction']) || $properties['old_reaction']) {
            $properties['old_reaction'] = (new ReactionTypeResolve())->resolveCollection($properties['old_reaction']);
        }

        if (isset($properties['new_reaction']) || $properties['new_reaction']) {
            $properties['new_reaction'] = (new ReactionTypeResolve())->resolveCollection($properties['new_reaction']);
        }

        return $properties;
    }
}
