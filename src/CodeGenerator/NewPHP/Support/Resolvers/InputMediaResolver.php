<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\InputMediaAnimation;
use Appto\TelegramBot\Data\InputMediaAudio;
use Appto\TelegramBot\Data\InputMediaDocument;
use Appto\TelegramBot\Data\InputMediaPhoto;
use Appto\TelegramBot\Data\InputMediaVideo;

class InputMediaResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'animation' => InputMediaAnimation::class,
            'document' => InputMediaDocument::class,
            'audio' => InputMediaAudio::class,
            'photo' => InputMediaPhoto::class,
            'video' => InputMediaVideo::class,
        ]);
    }
}
