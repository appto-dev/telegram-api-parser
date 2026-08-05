<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\BackgroundTypeChatTheme;
use Appto\TelegramBot\Type\BackgroundTypeFill;
use Appto\TelegramBot\Type\BackgroundTypePattern;
use Appto\TelegramBot\Type\BackgroundTypeWallpaper;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class BackgroundTypeCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'fill' => BackgroundTypeFill::from($value),
            'wallpaper' => BackgroundTypeWallpaper::from($value),
            'pattern' => BackgroundTypePattern::from($value),
            'chat_theme' => BackgroundTypeChatTheme::from($value),
            default => Uncastable::create(),
        };
    }
}
