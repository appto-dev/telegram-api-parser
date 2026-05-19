<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\ReplyMarkupResolver;

class SendVideoNote
{
    /**
     * for: reply_markup (ReplyMarkup)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['reply_markup']) || !$properties['reply_markup']) {
            return $properties;
        }

        $properties['reply_markup'] = (new ReplyMarkupResolver())->resolve($properties['reply_markup']);

        return $properties;
    }
}
