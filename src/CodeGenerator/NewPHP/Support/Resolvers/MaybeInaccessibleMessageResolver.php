<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\InaccessibleMessage;
use Appto\TelegramBot\Data\Message;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

class MaybeInaccessibleMessageResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function resolve($payload): mixed
    {
        if (!is_array($payload)) {
            if ($payload instanceof Data) {
                $payload = $payload->toArray();
            } else {
                throw new InvalidArgumentException('Expected array or Data object.');
            }
        }

        return $payload['date'] === 0
            ? InaccessibleMessage::from($payload)
            : Message::from($payload);
    }
}
