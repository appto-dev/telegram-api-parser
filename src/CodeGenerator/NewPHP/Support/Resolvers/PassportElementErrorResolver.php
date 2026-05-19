<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\PassportElementErrorDataField;
use Appto\TelegramBot\Data\PassportElementErrorFile;
use Appto\TelegramBot\Data\PassportElementErrorFiles;
use Appto\TelegramBot\Data\PassportElementErrorFrontSide;
use Appto\TelegramBot\Data\PassportElementErrorReverseSide;
use Appto\TelegramBot\Data\PassportElementErrorSelfie;
use Appto\TelegramBot\Data\PassportElementErrorTranslationFile;
use Appto\TelegramBot\Data\PassportElementErrorTranslationFiles;
use Appto\TelegramBot\Data\PassportElementErrorUnspecified;

class PassportElementErrorResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'data' => PassportElementErrorDataField::class,
            'front_side' => PassportElementErrorFrontSide::class,
            'reverse_side' => PassportElementErrorReverseSide::class,
            'selfie' => PassportElementErrorSelfie::class,
            'file' => PassportElementErrorFile::class,
            'files' => PassportElementErrorFiles::class,
            'translation_file' => PassportElementErrorTranslationFile::class,
            'translation_files' => PassportElementErrorTranslationFiles::class,
            'unspecified' => PassportElementErrorUnspecified::class,
        ], 'source');
    }
}
