<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\TelegramType\InputMediaAnimation;
use Appto\TelegramBot\TelegramType\InputMediaLink;
use Appto\TelegramBot\TelegramType\InputMediaLivePhoto;
use Appto\TelegramBot\TelegramType\InputMediaLocation;
use Appto\TelegramBot\TelegramType\InputMediaPhoto;
use Appto\TelegramBot\TelegramType\InputMediaSticker;
use Appto\TelegramBot\TelegramType\InputMediaVenue;
use Appto\TelegramBot\TelegramType\InputMediaVideo;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class InputPollOptionMediaCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'animation' => InputMediaAnimation::from($value),
            'link' => InputMediaLink::from($value),
            'live_photo' => InputMediaLivePhoto::from($value),
            'location' => InputMediaLocation::from($value),
            'photo' => InputMediaPhoto::from($value),
            'sticker' => InputMediaSticker::from($value),
            'venue' => InputMediaVenue::from($value),
            'video' => InputMediaVideo::from($value),
            default => Uncastable::create(),
        };
    }
}
