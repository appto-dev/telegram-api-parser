<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\InputMediaAnimation;
use Appto\TelegramBot\Type\InputMediaAudio;
use Appto\TelegramBot\Type\InputMediaDocument;
use Appto\TelegramBot\Type\InputMediaLivePhoto;
use Appto\TelegramBot\Type\InputMediaPhoto;
use Appto\TelegramBot\Type\InputMediaVideo;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class InputMediaCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'animation' => InputMediaAnimation::from($value),
            'audio' => InputMediaAudio::from($value),
            'document' => InputMediaDocument::from($value),
            'live_photo' => InputMediaLivePhoto::from($value),
            'photo' => InputMediaPhoto::from($value),
            'video' => InputMediaVideo::from($value),
            default => Uncastable::create(),
        };
    }
}
