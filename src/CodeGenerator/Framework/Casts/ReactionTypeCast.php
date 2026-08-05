<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\ReactionTypeCustomEmoji;
use Appto\TelegramBot\Type\ReactionTypeEmoji;
use Appto\TelegramBot\Type\ReactionTypePaid;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class ReactionTypeCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'emoji' => ReactionTypeEmoji::from($value),
            'custom_emoji' => ReactionTypeCustomEmoji::from($value),
            'paid' => ReactionTypePaid::from($value),
            default => Uncastable::create(),
        };
    }
}
