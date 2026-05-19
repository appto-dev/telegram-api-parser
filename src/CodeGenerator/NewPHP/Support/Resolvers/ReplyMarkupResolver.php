<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\ForceReply;
use Appto\TelegramBot\Data\InlineKeyboardMarkup;
use Appto\TelegramBot\Data\ReplyKeyboardMarkup;
use Appto\TelegramBot\Data\ReplyKeyboardRemove;
use Appto\TelegramBot\Interfaces\ReplyMarkup;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

class ReplyMarkupResolver extends GenericResolver
{
    public function __construct() {
        parent::__construct([]);
    }

    public function resolve($payload): ReplyMarkup
    {
        if (!is_array($payload)) {
            if ($payload instanceof Data) {
                $payload = $payload->toArray();
            } else {
                throw new InvalidArgumentException('Expected array or Data object.');
            }
        }

        return match (true) {
            isset($payload['inline_keyboard']) => InlineKeyboardMarkup::from($payload),
            isset($payload['keyboard']) => ReplyKeyboardMarkup::from($payload),
            isset($payload['remove_keyboard']) => ReplyKeyboardRemove::from($payload),
            isset($payload['force_reply']) => ForceReply::from($payload),
            default => throw new InvalidArgumentException('Invalid reply markup')
        };
    }
}
