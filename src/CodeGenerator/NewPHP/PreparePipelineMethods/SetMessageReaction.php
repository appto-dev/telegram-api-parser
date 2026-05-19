<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\ReactionTypeResolve;

class SetMessageReaction
{
    /**
     * for: reaction (ReactionType)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['reaction']) || !$properties['reaction']) {
            return $properties;
        }

        $properties['reaction'] = (new ReactionTypeResolve())->resolveCollection($properties['reaction']);

        return $properties;
    }
}
