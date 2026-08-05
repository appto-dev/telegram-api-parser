<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\MessageOriginChannel;
use Appto\TelegramBot\Type\MessageOriginChat;
use Appto\TelegramBot\Type\MessageOriginHiddenUser;
use Appto\TelegramBot\Type\MessageOriginUser;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class MessageOriginCast implements Cast
{
	public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
	{
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'user' => MessageOriginUser::from($value),
            'hidden_user' => MessageOriginHiddenUser::from($value),
            'chat' => MessageOriginChat::from($value),
            'channel' => MessageOriginChannel::from($value),
            default => Uncastable::create()
        };
	}
}
