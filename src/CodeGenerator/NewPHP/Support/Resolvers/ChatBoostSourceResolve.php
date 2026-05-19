<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\ChatBoostSourceGiftCode;
use Appto\TelegramBot\Data\ChatBoostSourceGiveaway;
use Appto\TelegramBot\Data\ChatBoostSourcePremium;

class ChatBoostSourceResolve extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'premium' => ChatBoostSourcePremium::class,
            'gift_code' => ChatBoostSourceGiftCode::class,
            'giveaway' => ChatBoostSourceGiveaway::class,
        ], 'source');
    }
}
