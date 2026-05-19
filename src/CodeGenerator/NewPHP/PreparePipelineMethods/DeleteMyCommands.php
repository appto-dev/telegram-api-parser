<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\BotCommandScopeResolver;

class DeleteMyCommands
{
    /**
     * for: scope (BotCommandScope)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['scope']) || !$properties['scope']) {
            return $properties;
        }

        $properties['scope'] = (new BotCommandScopeResolver())->resolve($properties['scope']);

        return $properties;
    }
}
