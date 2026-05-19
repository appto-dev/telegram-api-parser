<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\InputMessageContentResolver;

class InlineQueryResultLocation
{
    /**
     * for: input_message_content (InputMessageContent)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['input_message_content']) || !$properties['input_message_content']) {
            return $properties;
        }

        $properties['input_message_content'] = (new InputMessageContentResolver())
            ->resolve($properties['input_message_content']);

        return $properties;
    }
}
