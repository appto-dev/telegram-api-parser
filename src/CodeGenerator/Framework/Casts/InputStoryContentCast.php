<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\InputStoryContentPhoto;
use Appto\TelegramBot\Type\InputStoryContentVideo;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class InputStoryContentCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'photo' => InputStoryContentPhoto::from($value),
            'video' => InputStoryContentVideo::from($value),
            default => Uncastable::create(),
        };
    }
}
