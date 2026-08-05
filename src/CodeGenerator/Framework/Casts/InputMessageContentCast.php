<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\InputContactMessageContent;
use Appto\TelegramBot\Type\InputInvoiceMessageContent;
use Appto\TelegramBot\Type\InputLocationMessageContent;
use Appto\TelegramBot\Type\InputRichMessageContent;
use Appto\TelegramBot\Type\InputTextMessageContent;
use Appto\TelegramBot\Type\InputVenueMessageContent;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class InputMessageContentCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match (true) {
            isset($value['message_text']) => InputTextMessageContent::from($value),
            isset($value['rich_message']) => InputRichMessageContent::from($value),
            isset($value['latitude']) && isset($value['longitude']) => InputLocationMessageContent::from($value),
            isset($value['latitude']) && isset($value['longitude']) && isset($value['title']) && isset($value['address']) => InputVenueMessageContent::from($value),
            isset($value['phone_number']) && isset($value['first_name']) => InputContactMessageContent::from($value),
            isset($value['title']) && isset($value['description']) && isset($value['payload']) && isset($value['currency']) && isset($value['prices']) => InputInvoiceMessageContent::from($value),
            default => Uncastable::create(),
        };
    }
}
