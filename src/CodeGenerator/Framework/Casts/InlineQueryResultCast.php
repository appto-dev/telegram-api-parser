<?php

declare(strict_types=1);

namespace Appto\TelegramBot\Support\Casts;

use Appto\TelegramBot\Type\InlineQueryResultArticle;
use Appto\TelegramBot\Type\InlineQueryResultAudio;
use Appto\TelegramBot\Type\InlineQueryResultCachedAudio;
use Appto\TelegramBot\Type\InlineQueryResultCachedDocument;
use Appto\TelegramBot\Type\InlineQueryResultCachedGif;
use Appto\TelegramBot\Type\InlineQueryResultCachedMpeg4Gif;
use Appto\TelegramBot\Type\InlineQueryResultCachedPhoto;
use Appto\TelegramBot\Type\InlineQueryResultCachedSticker;
use Appto\TelegramBot\Type\InlineQueryResultCachedVideo;
use Appto\TelegramBot\Type\InlineQueryResultCachedVoice;
use Appto\TelegramBot\Type\InlineQueryResultContact;
use Appto\TelegramBot\Type\InlineQueryResultDocument;
use Appto\TelegramBot\Type\InlineQueryResultGame;
use Appto\TelegramBot\Type\InlineQueryResultGif;
use Appto\TelegramBot\Type\InlineQueryResultLocation;
use Appto\TelegramBot\Type\InlineQueryResultMpeg4Gif;
use Appto\TelegramBot\Type\InlineQueryResultPhoto;
use Appto\TelegramBot\Type\InlineQueryResultVenue;
use Appto\TelegramBot\Type\InlineQueryResultVideo;
use Appto\TelegramBot\Type\InlineQueryResultVoice;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Uncastable;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class InlineQueryResultCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed {
        if (!is_array($value)) {
            return Uncastable::create();
        }

        return match (true) {
            $value['type'] == 'audio' && isset($value['audio_file_id']) => InlineQueryResultCachedAudio::from($value),
            $value['type'] == 'document' && isset($value['document_file_id']) => InlineQueryResultCachedDocument::from($value),
            $value['type'] == 'gif' && isset($value['gif_file_id']) => InlineQueryResultCachedGif::from($value),
            $value['type'] == 'mpeg4_gif' && isset($value['mpeg4_file_id']) => InlineQueryResultCachedMpeg4Gif::from($value),
            $value['type'] == 'photo' && isset($value['photo_file_id']) => InlineQueryResultCachedPhoto::from($value),
            $value['type'] == 'sticker' && isset($value['sticker_file_id']) => InlineQueryResultCachedSticker::from($value),
            $value['type'] == 'video' && isset($value['video_file_id']) => InlineQueryResultCachedVideo::from($value),
            $value['type'] == 'voice' && isset($value['voice_file_id']) => InlineQueryResultCachedVoice::from($value),
            $value['type'] == 'article' => InlineQueryResultArticle::from($value),
            $value['type'] == 'audio' => InlineQueryResultAudio::from($value),
            $value['type'] == 'contact' => InlineQueryResultContact::from($value),
            $value['type'] == 'game' => InlineQueryResultGame::from($value),
            $value['type'] == 'document' => InlineQueryResultDocument::from($value),
            $value['type'] == 'gif' => InlineQueryResultGif::from($value),
            $value['type'] == 'location' => InlineQueryResultLocation::from($value),
            $value['type'] == 'mpeg4_gif' => InlineQueryResultMpeg4Gif::from($value),
            $value['type'] == 'photo' => InlineQueryResultPhoto::from($value),
            $value['type'] == 'venue' => InlineQueryResultVenue::from($value),
            $value['type'] == 'video' => InlineQueryResultVideo::from($value),
            $value['type'] == 'voice' => InlineQueryResultVoice::from($value),
            default => Uncastable::create(),
        };
    }
}
