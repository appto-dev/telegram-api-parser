<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\TransactionPartnerAffiliateProgram;
use Appto\TelegramBot\Type\TransactionPartnerChat;
use Appto\TelegramBot\Type\TransactionPartnerFragment;
use Appto\TelegramBot\Type\TransactionPartnerOther;
use Appto\TelegramBot\Type\TransactionPartnerTelegramAds;
use Appto\TelegramBot\Type\TransactionPartnerTelegramApi;
use Appto\TelegramBot\Type\TransactionPartnerUser;
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
