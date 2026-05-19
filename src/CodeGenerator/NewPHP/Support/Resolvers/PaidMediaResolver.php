<?php

namespace Appto\TelegramBot\Support\Resolvers;


use Appto\TelegramBot\Data\PaidMediaPhoto;
use Appto\TelegramBot\Data\PaidMediaPreview;
use Appto\TelegramBot\Data\PaidMediaVideo;

class PaidMediaResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'preview' => PaidMediaPreview::class,
            'photo' => PaidMediaPhoto::class,
            'video' => PaidMediaVideo::class,
        ]);
    }
}
