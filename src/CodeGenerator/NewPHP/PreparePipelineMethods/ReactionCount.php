<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\ReactionTypeResolve;

class ReactionCount
{
    /**
     * for: type (ReactionType)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['type']) || !$properties['type']) {
            return $properties;
        }

        $properties['type'] = (new ReactionTypeResolve())->resolve($properties['type']);

        return $properties;
    }
}
