<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\MaybeInaccessibleMessageResolver;
use Appto\TelegramBot\Support\Resolvers\MessageOriginResolver;

class Message
{
    /**
     * for: forward_origin, pinned_message (MessageOrigin, MaybeInaccessibleMessage)
     */
    public function __invoke(array $properties): array
    {
        if (isset($properties['forward_origin']) && $properties['forward_origin']) {
            $properties['forward_origin'] = (new MessageOriginResolver())->resolve($properties['forward_origin']);
        }

        if (isset($properties['pinned_message']) && $properties['pinned_message']) {
            $properties['pinned_message'] = $properties['message'] = (new MaybeInaccessibleMessageResolver())
                ->resolve($properties['pinned_message']);
        }

        return $properties;
    }
}
