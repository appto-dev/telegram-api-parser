<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\OwnedGiftResolver;

class OwnedGifts
{
    /**
     * for: gifts (OwnedGift)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['gifts']) || !$properties['gifts']) {
            return $properties;
        }

        $properties['gifts'] = (new OwnedGiftResolver())->resolveCollection($properties['gifts']);

        return $properties;
    }
}
