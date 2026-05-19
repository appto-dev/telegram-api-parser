<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\StoryAreaTypeLink;
use Appto\TelegramBot\Data\StoryAreaTypeLocation;
use Appto\TelegramBot\Data\StoryAreaTypeSuggestedReaction;
use Appto\TelegramBot\Data\StoryAreaTypeUniqueGift;
use Appto\TelegramBot\Data\StoryAreaTypeWeather;

class StoryAreaTypeResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'location' => StoryAreaTypeLocation::class,
            'suggested_reaction' => StoryAreaTypeSuggestedReaction::class,
            'link' => StoryAreaTypeLink::class,
            'weather' => StoryAreaTypeWeather::class,
            'unique_gift' => StoryAreaTypeUniqueGift::class,
        ]);
    }
}
