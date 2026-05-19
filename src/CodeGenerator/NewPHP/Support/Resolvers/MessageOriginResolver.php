<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\MessageOriginChannel;
use Appto\TelegramBot\Data\MessageOriginChat;
use Appto\TelegramBot\Data\MessageOriginHiddenUser;
use Appto\TelegramBot\Data\MessageOriginUser;

class MessageOriginResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'user' => MessageOriginUser::class,
            'hidden_user' => MessageOriginHiddenUser::class,
            'chat' => MessageOriginChat::class,
            'channel' => MessageOriginChannel::class,
        ]);
    }
}
