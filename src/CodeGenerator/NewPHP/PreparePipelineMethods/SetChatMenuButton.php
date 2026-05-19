<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\MenuButtonResolver;

class SetChatMenuButton
{
    /**
     * for: menu_button (MenuButton)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['menu_button']) || !$properties['menu_button']) {
            return $properties;
        }

        $properties['menu_button'] = (new MenuButtonResolver())->resolve($properties['menu_button']);

        return $properties;
    }
}
