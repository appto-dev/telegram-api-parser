<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\InputPaidMediaResolver;
use Appto\TelegramBot\Support\Resolvers\ReplyMarkupResolver;

class SendPaidMedia
{
    /**
     * for: media, reply_markup (InputPaidMedia, ReplyMarkup)
     */
    public function __invoke(array $properties): array
    {
        if (isset($properties['reply_markup']) && $properties['reply_markup']) {
            $properties['reply_markup'] = (new ReplyMarkupResolver())->resolve($properties['reply_markup']);
        }

        if (isset($properties['media']) && $properties['media']) {
            $properties['media'] = (new InputPaidMediaResolver())->resolve($properties['media']);
        }

        return $properties;
    }
}
