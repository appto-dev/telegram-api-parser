<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\ReactionTypeCustomEmoji;
use Appto\TelegramBot\Data\ReactionTypeEmoji;
use Appto\TelegramBot\Data\ReactionTypePaid;

class ReactionTypeResolve extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'emoji' => ReactionTypeEmoji::class,
            'custom_emoji' => ReactionTypeCustomEmoji::class,
            'paid' => ReactionTypePaid::class,
        ]);
    }
}
