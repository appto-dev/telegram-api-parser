<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\MessageOriginResolver;

class ExternalReplyInfo
{
    /**
     * for: origin (MessageOrigin)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['origin']) || !$properties['origin']) {
            return $properties;
        }

        $properties['origin'] = (new MessageOriginResolver())->resolve($properties['origin']);

        return $properties;
    }
}
