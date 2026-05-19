<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\InputProfilePhotoResolver;

class SetMyProfilePhoto
{
    /**
     * for: photo (InputProfilePhoto)
     */
    public function __invoke(array $properties): array
    {
        if (!isset($properties['photo']) || !$properties['photo']) {
            return $properties;
        }

        $properties['photo'] = (new InputProfilePhotoResolver())->resolve($properties['photo']);

        return $properties;
    }
}
