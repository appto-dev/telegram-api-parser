<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\InputContactMessageContent;
use Appto\TelegramBot\Data\InputInvoiceMessageContent;
use Appto\TelegramBot\Data\InputLocationMessageContent;
use Appto\TelegramBot\Data\InputTextMessageContent;
use Appto\TelegramBot\Data\InputVenueMessageContent;
use Appto\TelegramBot\Interfaces\InputMessageContent;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

class InputMessageContentResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function resolve($payload): InputMessageContent
    {
        if (!is_array($payload)) {
            if ($payload instanceof Data) {
                $payload = $payload->toArray();
            } else {
                throw new InvalidArgumentException('Expected array or Data object.');
            }
        }

        return match (true) {
            !empty($payload['message_text']) => InputTextMessageContent::from($payload),
            isset($payload['latitude']) && isset($payload['longitude']) && !isset($payload['title']) => InputLocationMessageContent::from($payload),
            isset($payload['latitude']) && isset($payload['longitude']) && isset($payload['title']) && isset($payload['address']) => InputVenueMessageContent::from($payload),
            isset($payload['phone_number']) && isset($payload['first_name']) => InputContactMessageContent::from($payload),
            isset($payload['title']) && isset($payload['currency']) => InputInvoiceMessageContent::from($payload),
        };
    }
}
