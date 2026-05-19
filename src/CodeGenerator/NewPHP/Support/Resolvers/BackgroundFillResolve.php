<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\BackgroundFillFreeformGradient;
use Appto\TelegramBot\Data\BackgroundFillGradient;
use Appto\TelegramBot\Data\BackgroundFillSolid;

class BackgroundFillResolve extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'solid' => BackgroundFillSolid::class,
            'gradient' => BackgroundFillGradient::class,
            'freeform_gradient' => BackgroundFillFreeformGradient::class,
        ], 'fill');
    }
}
