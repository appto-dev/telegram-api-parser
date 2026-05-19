<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\BackgroundFillResolve;

class BackgroundTypeFill
{
    /**
     * for: fill (BackgroundFill)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['fill']) || !$properties['fill']) {
            return $properties;
        }

        $properties['fill'] = (new BackgroundFillResolve())->resolve($properties['fill']);

        return $properties;
    }
}
