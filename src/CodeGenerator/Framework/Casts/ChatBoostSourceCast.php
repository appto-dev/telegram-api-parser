<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\ChatBoostSourceGiftCode;
use Appto\TelegramBot\Type\ChatBoostSourceGiveaway;
use Appto\TelegramBot\Type\ChatBoostSourcePremium;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class ChatBoostSourceCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['source']) {
            'premium' => ChatBoostSourcePremium::from($value),
            'gift_code' => ChatBoostSourceGiftCode::from($value),
            'giveaway' => ChatBoostSourceGiveaway::from($value),
            default => Uncastable::create(),
        };
    }
}
