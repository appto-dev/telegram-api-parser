<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\StoryAreaTypeLink;
use Appto\TelegramBot\Type\StoryAreaTypeLocation;
use Appto\TelegramBot\Type\StoryAreaTypeSuggestedReaction;
use Appto\TelegramBot\Type\StoryAreaTypeUniqueGift;
use Appto\TelegramBot\Type\StoryAreaTypeWeather;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class StoryAreaTypeCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'location' => StoryAreaTypeLocation::from($value),
            'suggested_reaction' => StoryAreaTypeSuggestedReaction::from($value),
            'link' => StoryAreaTypeLink::from($value),
            'weather' => StoryAreaTypeWeather::from($value),
            'unique_gift' => StoryAreaTypeUniqueGift::from($value),
            default => Uncastable::create(),
        };
    }
}
