<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\PaidMediaResolver;

class PaidMediaInfo
{
    /**
     * for: paid_media (PaidMedia)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['paid_media']) || !$properties['paid_media']) {
            return $properties;
        }

        $properties['paid_media'] = (new PaidMediaResolver())->resolveCollection($properties['paid_media']);

        return $properties;
    }
}
