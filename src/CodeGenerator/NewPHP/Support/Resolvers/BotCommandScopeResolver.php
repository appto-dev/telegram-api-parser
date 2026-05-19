<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\BotCommandScopeAllGroupChats;
use Appto\TelegramBot\Data\BotCommandScopeAllPrivateChats;
use Appto\TelegramBot\Data\BotCommandScopeChat;
use Appto\TelegramBot\Data\BotCommandScopeChatAdministrators;
use Appto\TelegramBot\Data\BotCommandScopeChatMember;
use Appto\TelegramBot\Data\BotCommandScopeDefault;

class BotCommandScopeResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'default' => BotCommandScopeDefault::class,
            'all_private_chats' => BotCommandScopeAllPrivateChats::class,
            'all_group_chats' => BotCommandScopeAllGroupChats::class,
            'all_chat_administrators' => BotCommandScopeAllGroupChats::class,
            'chat' => BotCommandScopeChat::class,
            'chat_administrators' => BotCommandScopeChatAdministrators::class,
            'chat_member' => BotCommandScopeChatMember::class,
        ], 'scope');
    }
}
