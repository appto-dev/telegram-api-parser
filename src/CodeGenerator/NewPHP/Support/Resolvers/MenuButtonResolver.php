<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\MenuButtonCommands;
use Appto\TelegramBot\Data\MenuButtonDefault;
use Appto\TelegramBot\Data\MenuButtonWebApp;

class MenuButtonResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'commands' => MenuButtonCommands::class,
            'web_app' => MenuButtonWebApp::class,
            'default' => MenuButtonDefault::class,
        ]);
    }
}
