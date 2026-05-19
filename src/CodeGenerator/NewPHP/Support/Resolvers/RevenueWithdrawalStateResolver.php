<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\RevenueWithdrawalStateFailed;
use Appto\TelegramBot\Data\RevenueWithdrawalStatePending;
use Appto\TelegramBot\Data\RevenueWithdrawalStateSucceeded;

class RevenueWithdrawalStateResolver extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'pending' => RevenueWithdrawalStatePending::class,
            'succeeded' => RevenueWithdrawalStateSucceeded::class,
            'failed' => RevenueWithdrawalStateFailed::class,
        ]);
    }
}
