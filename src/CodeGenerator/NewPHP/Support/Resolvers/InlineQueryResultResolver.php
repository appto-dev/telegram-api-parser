<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\InlineQueryResultArticle;
use Appto\TelegramBot\Data\InlineQueryResultAudio;
use Appto\TelegramBot\Data\InlineQueryResultCachedAudio;
use Appto\TelegramBot\Data\InlineQueryResultCachedDocument;
use Appto\TelegramBot\Data\InlineQueryResultCachedGif;
use Appto\TelegramBot\Data\InlineQueryResultCachedMpeg4Gif;
use Appto\TelegramBot\Data\InlineQueryResultCachedPhoto;
use Appto\TelegramBot\Data\InlineQueryResultCachedSticker;
use Appto\TelegramBot\Data\InlineQueryResultCachedVideo;
use Appto\TelegramBot\Data\InlineQueryResultCachedVoice;
use Appto\TelegramBot\Data\InlineQueryResultContact;
use Appto\TelegramBot\Data\InlineQueryResultDocument;
use Appto\TelegramBot\Data\InlineQueryResultGame;
use Appto\TelegramBot\Data\InlineQueryResultGif;
use Appto\TelegramBot\Data\InlineQueryResultLocation;
use Appto\TelegramBot\Data\InlineQueryResultMpeg4Gif;
use Appto\TelegramBot\Data\InlineQueryResultPhoto;
use Appto\TelegramBot\Data\InlineQueryResultVenue;
use Appto\TelegramBot\Data\InlineQueryResultVideo;
use Appto\TelegramBot\Data\InlineQueryResultVoice;
use Appto\TelegramBot\Interfaces\InlineQueryResult;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

class InlineQueryResultResolver extends GenericResolver
{
    public function __construct() {
        parent::__construct([]);
    }

    public function resolve($payload): InlineQueryResult
    {
        if (!is_array($payload)) {
            if ($payload instanceof Data) {
                $payload = $payload->toArray();
            } else {
                throw new InvalidArgumentException('Expected array or Data object.');
            }
        }

        return match (true) {
            $payload['type'] == 'audio' => isset($payload['audio_file_id']) ? InlineQueryResultCachedAudio::from($payload) : InlineQueryResultAudio::from($payload),
            $payload['type'] == 'document' => isset($payload['document_file_id']) ? InlineQueryResultCachedDocument::from($payload) : InlineQueryResultDocument::from($payload),
            $payload['type'] == 'gif' => isset($payload['gif_file_id']) ? InlineQueryResultCachedGif::from($payload) : InlineQueryResultGif::from($payload),
            $payload['type'] == 'mpeg4_gif' => isset($payload['mpeg4_file_id']) ? InlineQueryResultCachedMpeg4Gif::from($payload) : InlineQueryResultMpeg4Gif::from($payload),
            $payload['type'] == 'photo' => isset($payload['photo_file_id']) ? InlineQueryResultCachedPhoto::from($payload) : InlineQueryResultPhoto::from($payload),
            $payload['type'] == 'video' => isset($payload['video_file_id']) ? InlineQueryResultCachedVideo::from($payload) : InlineQueryResultVideo::from($payload),
            $payload['type'] == 'voice' => isset($payload['voice_file_id']) ? InlineQueryResultCachedVoice::from($payload) : InlineQueryResultVoice::from($payload),
            $payload['type'] == 'sticker' => InlineQueryResultCachedSticker::from($payload),
            $payload['type'] == 'article' => InlineQueryResultArticle::from($payload),
            $payload['type'] == 'contact' => InlineQueryResultContact::from($payload),
            $payload['type'] == 'game' => InlineQueryResultGame::from($payload),
            $payload['type'] == 'location' => InlineQueryResultLocation::from($payload),
            $payload['type'] == 'venue' => InlineQueryResultVenue::from($payload),
        };
    }
}
