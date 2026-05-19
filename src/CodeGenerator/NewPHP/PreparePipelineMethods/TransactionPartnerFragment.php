<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\RevenueWithdrawalStateResolver;

class TransactionPartnerFragment
{
    /**
     * for: withdrawal_state (RevenueWithdrawalState)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['withdrawal_state']) || !$properties['withdrawal_state']) {
            return $properties;
        }

        $properties['withdrawal_state'] = (new RevenueWithdrawalStateResolver())
            ->resolve($properties['withdrawal_state']);

        return $properties;
    }
}
