<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\InlineQueryResultResolver;

class AnswerInlineQuery
{
    /**
     * for: results (InlineQueryResult)
     */
    public function __invoke(array $properties): array
    {
        if (!$properties['results'] || !is_array($properties['results'])) {
            return $properties;
        }

        $properties['results'] = (new InlineQueryResultResolver)->resolveCollection($properties['results']);

        return $properties;
    }
}
