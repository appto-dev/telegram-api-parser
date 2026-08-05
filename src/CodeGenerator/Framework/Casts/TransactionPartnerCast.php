<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\TelegramType\TransactionPartnerAffiliateProgram;
use Appto\TelegramBot\TelegramType\TransactionPartnerChat;
use Appto\TelegramBot\TelegramType\TransactionPartnerFragment;
use Appto\TelegramBot\TelegramType\TransactionPartnerOther;
use Appto\TelegramBot\TelegramType\TransactionPartnerTelegramAds;
use Appto\TelegramBot\TelegramType\TransactionPartnerTelegramApi;
use Appto\TelegramBot\TelegramType\TransactionPartnerUser;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class TransactionPartnerCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'user' => TransactionPartnerUser::from($value),
            'chat' => TransactionPartnerChat::from($value),
            'affiliate_program' => TransactionPartnerAffiliateProgram::from($value),
            'fragment' => TransactionPartnerFragment::from($value),
            'telegram_ads' => TransactionPartnerTelegramAds::from($value),
            'telegram_api' => TransactionPartnerTelegramApi::from($value),
            'other' => TransactionPartnerOther::from($value),
            default => Uncastable::create(),
        };
    }
}
