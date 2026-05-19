<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\TransactionPartnerAffiliateProgram;
use Appto\TelegramBot\Data\TransactionPartnerChat;
use Appto\TelegramBot\Data\TransactionPartnerFragment;
use Appto\TelegramBot\Data\TransactionPartnerOther;
use Appto\TelegramBot\Data\TransactionPartnerTelegramAds;
use Appto\TelegramBot\Data\TransactionPartnerTelegramApi;
use Appto\TelegramBot\Data\TransactionPartnerUser;

class TransactionPartnerResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'user' => TransactionPartnerUser::class,
            'chat' => TransactionPartnerChat::class,
            'affiliate_program' => TransactionPartnerAffiliateProgram::class,
            'fragment' => TransactionPartnerFragment::class,
            'telegram_ads' => TransactionPartnerTelegramAds::class,
            'telegram_api' => TransactionPartnerTelegramApi::class,
            'other' => TransactionPartnerOther::class,
        ]);
    }
}
