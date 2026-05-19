<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\PassportElementErrorResolver;

class SetPassportDataErrors
{
    /**
     * for: errors (PassportElementError)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['errors']) || !$properties['errors']) {
            return $properties;
        }

        $properties['errors'] = (new PassportElementErrorResolver())->resolveCollection($properties['errors']);

        return $properties;
    }
}
