<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\ChatMemberAdministrator;
use Appto\TelegramBot\Type\ChatMemberBanned;
use Appto\TelegramBot\Type\ChatMemberLeft;
use Appto\TelegramBot\Type\ChatMemberMember;
use Appto\TelegramBot\Type\ChatMemberOwner;
use Appto\TelegramBot\Type\ChatMemberRestricted;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class ChatMemberCast implements Cast
{
	public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
	{
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['status']) {
            'creator' => ChatMemberOwner::from($value),
            'administrator' => ChatMemberAdministrator::from($value),
            'member' => ChatMemberMember::from($value),
            'restricted' => ChatMemberRestricted::from($value),
            'left' => ChatMemberLeft::from($value),
            'kicked' => ChatMemberBanned::from($value),
            default => Uncastable::create(),
        };
	}
}
