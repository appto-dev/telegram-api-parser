<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\TelegramType\RichTextAnchor;
use Appto\TelegramBot\TelegramType\RichTextAnchorLink;
use Appto\TelegramBot\TelegramType\RichTextBankCardNumber;
use Appto\TelegramBot\TelegramType\RichTextBold;
use Appto\TelegramBot\TelegramType\RichTextBotCommand;
use Appto\TelegramBot\TelegramType\RichTextCashtag;
use Appto\TelegramBot\TelegramType\RichTextCode;
use Appto\TelegramBot\TelegramType\RichTextCustomEmoji;
use Appto\TelegramBot\TelegramType\RichTextDateTime;
use Appto\TelegramBot\TelegramType\RichTextEmailAddress;
use Appto\TelegramBot\TelegramType\RichTextHashtag;
use Appto\TelegramBot\TelegramType\RichTextItalic;
use Appto\TelegramBot\TelegramType\RichTextMarked;
use Appto\TelegramBot\TelegramType\RichTextMathematicalExpression;
use Appto\TelegramBot\TelegramType\RichTextMention;
use Appto\TelegramBot\TelegramType\RichTextPhoneNumber;
use Appto\TelegramBot\TelegramType\RichTextReference;
use Appto\TelegramBot\TelegramType\RichTextReferenceLink;
use Appto\TelegramBot\TelegramType\RichTextSpoiler;
use Appto\TelegramBot\TelegramType\RichTextStrikethrough;
use Appto\TelegramBot\TelegramType\RichTextSubscript;
use Appto\TelegramBot\TelegramType\RichTextSuperscript;
use Appto\TelegramBot\TelegramType\RichTextTextMention;
use Appto\TelegramBot\TelegramType\RichTextUnderline;
use Appto\TelegramBot\TelegramType\RichTextUrl;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class RichTextCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match ($value['type']) {
            'bold' => RichTextBold::from($value),
            'italic' => RichTextItalic::from($value),
            'underline' => RichTextUnderline::from($value),
            'strikethrough' => RichTextStrikethrough::from($value),
            'spoiler' => RichTextSpoiler::from($value),
            'date_time' => RichTextDateTime::from($value),
            'text_mention' => RichTextTextMention::from($value),
            'subscript' => RichTextSubscript::from($value),
            'superscript' => RichTextSuperscript::from($value),
            'marked' => RichTextMarked::from($value),
            'code' => RichTextCode::from($value),
            'custom_emoji' => RichTextCustomEmoji::from($value),
            'mathematical_expression' => RichTextMathematicalExpression::from($value),
            'url' => RichTextUrl::from($value),
            'email_address' => RichTextEmailAddress::from($value),
            'phone_number' => RichTextPhoneNumber::from($value),
            'bank_card_number' => RichTextBankCardNumber::from($value),
            'mention' => RichTextMention::from($value),
            'hashtag' => RichTextHashtag::from($value),
            'cashtag' => RichTextCashtag::from($value),
            'bot_command' => RichTextBotCommand::from($value),
            'anchor' => RichTextAnchor::from($value),
            'anchor_link' => RichTextAnchorLink::from($value),
            'reference' => RichTextReference::from($value),
            'reference_link' => RichTextReferenceLink::from($value),
            default => Uncastable::create(),
        };
    }
}
