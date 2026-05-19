<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\InputPaidMediaPhoto;
use Appto\TelegramBot\Data\InputPaidMediaVideo;

class InputPaidMediaResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'photo' => InputPaidMediaPhoto::class,
            'video' => InputPaidMediaVideo::class,
        ]);
    }
}
