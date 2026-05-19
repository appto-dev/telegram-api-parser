<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\ReactionTypeResolve;

class ChatFullInfo
{
    /**
     * for: available_reactions (ReactionType)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['available_reactions']) || !$properties['available_reactions']) {
            return $properties;
        }

        $properties['available_reactions'] = (new ReactionTypeResolve())
            ->resolveCollection($properties['available_reactions']);

        return $properties;
    }
}
