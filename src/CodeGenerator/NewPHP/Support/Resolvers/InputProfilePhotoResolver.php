<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\InputProfilePhotoAnimated;
use Appto\TelegramBot\Data\InputProfilePhotoStatic;

class InputProfilePhotoResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'static' => InputProfilePhotoStatic::class,
            'animated' => InputProfilePhotoAnimated::class,
        ]);
    }
}
