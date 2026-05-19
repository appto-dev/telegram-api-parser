<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\ChatBoostSourceResolve;

class ChatBoost
{
    /**
     * for: source (ChatBoostSource)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['source']) || !$properties['source']) {
            return $properties;
        }

        $properties['source'] = (new ChatBoostSourceResolve())->resolve($properties['source']);

        return $properties;
    }
}
