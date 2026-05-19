<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\OwnedGiftRegular;
use Appto\TelegramBot\Data\OwnedGiftUnique;

class OwnedGiftResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'regular' => OwnedGiftRegular::class,
            'unique' => OwnedGiftUnique::class,
        ]);
    }
}
