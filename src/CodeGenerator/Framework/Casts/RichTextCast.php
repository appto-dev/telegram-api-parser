<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\RichTextAnchor;
use Appto\TelegramBot\Type\RichTextAnchorLink;
use Appto\TelegramBot\Type\RichTextBankCardNumber;
use Appto\TelegramBot\Type\RichTextBold;
use Appto\TelegramBot\Type\RichTextBotCommand;
use Appto\TelegramBot\Type\RichTextCashtag;
use Appto\TelegramBot\Type\RichTextCode;
use Appto\TelegramBot\Type\RichTextCustomEmoji;
use Appto\TelegramBot\Type\RichTextDateTime;
use Appto\TelegramBot\Type\RichTextEmailAddress;
use Appto\TelegramBot\Type\RichTextHashtag;
use Appto\TelegramBot\Type\RichTextItalic;
use Appto\TelegramBot\Type\RichTextMarked;
use Appto\TelegramBot\Type\RichTextMathematicalExpression;
use Appto\TelegramBot\Type\RichTextMention;
use Appto\TelegramBot\Type\RichTextPhoneNumber;
use Appto\TelegramBot\Type\RichTextReference;
use Appto\TelegramBot\Type\RichTextReferenceLink;
use Appto\TelegramBot\Type\RichTextSpoiler;
use Appto\TelegramBot\Type\RichTextStrikethrough;
use Appto\TelegramBot\Type\RichTextSubscript;
use Appto\TelegramBot\Type\RichTextSuperscript;
use Appto\TelegramBot\Type\RichTextTextMention;
use Appto\TelegramBot\Type\RichTextUnderline;
use Appto\TelegramBot\Type\RichTextUrl;
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
