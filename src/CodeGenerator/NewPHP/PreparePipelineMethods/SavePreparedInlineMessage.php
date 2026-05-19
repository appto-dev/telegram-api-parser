<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\InlineQueryResultResolver;

class SavePreparedInlineMessage
{
    /**
     * for: result (InlineQueryResult)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['result']) || !$properties['result']) {
            return $properties;
        }

        $properties['result'] = (new InlineQueryResultResolver())->resolve($properties['result']);

        return $properties;
    }
}
