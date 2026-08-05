<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\MenuButtonCommands;
use Appto\TelegramBot\Type\MenuButtonDefault;
use Appto\TelegramBot\Type\MenuButtonWebApp;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class MenuButtonCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'commands' => MenuButtonCommands::from($value),
            'web_app' => MenuButtonWebApp::from($value),
            'default' => MenuButtonDefault::from($value),
            default => Uncastable::create(),
        };
    }
}
