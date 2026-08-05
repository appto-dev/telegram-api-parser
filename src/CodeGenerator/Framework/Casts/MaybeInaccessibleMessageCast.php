<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\InaccessibleMessage;
use Appto\TelegramBot\Type\Message;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class MaybeInaccessibleMessageCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        // Telegram: date === 0 у InaccessibleMessage, иначе полноценный Message
        return isset($value['date']) && $value['date'] > 0
            ? Message::from($value)
            : InaccessibleMessage::from($value);
    }
}
