<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\BackgroundTypeFill;
use Appto\TelegramBot\Data\BackgroundTypePattern;
use Appto\TelegramBot\Data\BackgroundTypeWallpaper;

class BackgroundTypeResolve extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'fill' => BackgroundTypeFill::class,
            'wallpaper' => BackgroundTypeWallpaper::class,
            'pattern' => BackgroundTypePattern::class,
        ]);
    }
}
