<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\InputProfilePhotoAnimated;
use Appto\TelegramBot\Type\InputProfilePhotoStatic;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class InputProfilePhotoCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'static' => InputProfilePhotoStatic::from($value),
            'animated' => InputProfilePhotoAnimated::from($value),
            default => Uncastable::create(),
        };
    }
}
