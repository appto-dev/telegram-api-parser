<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\TransactionPartnerResolver;

class StarTransaction
{
    /**
     * for: source (TransactionPartner)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['source']) || !$properties['source']) {
            return $properties;
        }

        $properties['source'] = (new TransactionPartnerResolver())->resolve($properties['source']);

        return $properties;
    }
}
