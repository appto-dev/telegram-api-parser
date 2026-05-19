<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\InputStoryContentPhoto;
use Appto\TelegramBot\Data\InputStoryContentVideo;

class InputStoryContentResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'photo' => InputStoryContentPhoto::class,
            'video' => InputStoryContentVideo::class,
        ]);
    }
}
