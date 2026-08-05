<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\BackgroundFillFreeformGradient;
use Appto\TelegramBot\Type\BackgroundFillGradient;
use Appto\TelegramBot\Type\BackgroundFillSolid;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class BackgroundFillCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'solid' => BackgroundFillSolid::from($value),
            'gradient' => BackgroundFillGradient::from($value),
            'freeform_gradient' => BackgroundFillFreeformGradient::from($value),
            default => Uncastable::create(),
        };
    }
}
