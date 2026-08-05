<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\TelegramType\BotCommandScopeAllChatAdministrators;
use Appto\TelegramBot\TelegramType\BotCommandScopeAllGroupChats;
use Appto\TelegramBot\TelegramType\BotCommandScopeAllPrivateChats;
use Appto\TelegramBot\TelegramType\BotCommandScopeChat;
use Appto\TelegramBot\TelegramType\BotCommandScopeChatAdministrators;
use Appto\TelegramBot\TelegramType\BotCommandScopeChatMember;
use Appto\TelegramBot\TelegramType\BotCommandScopeDefault;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class BotCommandScopeCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'default' => BotCommandScopeDefault::from($value),
            'all_private_chats' => BotCommandScopeAllPrivateChats::from($value),
            'all_group_chats' => BotCommandScopeAllGroupChats::from($value),
            'all_chat_administrators' => BotCommandScopeAllChatAdministrators::from($value),
            'chat' => BotCommandScopeChat::from($value),
            'chat_administrators' => BotCommandScopeChatAdministrators::from($value),
            'chat_member' => BotCommandScopeChatMember::from($value),
            default => Uncastable::create(),
        };
    }
}
