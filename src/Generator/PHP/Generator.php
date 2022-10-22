<?php

namespace TelegramApiParser\Generator\PHP;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\PhpNamespace;
use TelegramApiParser\Exceptions\GeneratorException;
use TelegramApiParser\Generator\GeneratorLibraryInterface;
use TelegramApiParser\Helpers;

class Generator implements GeneratorLibraryInterface
{
    /**
     * @throws GeneratorException
     */
    public function run(string $filename): void
    {
        if (!file_exists($filename)) {
            throw new GeneratorException('Please run `php console telegram:json`.');
        }

        $json = json_decode(file_get_contents($filename));

        if (json_last_error()) {
            throw new GeneratorException('Parse JSON: '. json_last_error_msg());
        }

        self::abstracts();

        foreach ($json->items as $item) {
            self::create($item, [
                'version' => $json->version,
                'author' => 'Sergey Makhlenko <https://github.com/mahlenko>',
            ]);
        }
    }

    /**
     * @param $item
     * @param array $comments
     * @return void
     */
    private function create($item, array $comments = []): void
    {
        /* Create interfaces */
        $namespace = Helpers::namespace(PhpPaths::Interface->name);
        $interface = self::interfaceBuild($namespace, $item, $comments);

        foreach($item->data as $method) {
            /* Telegram type */
            if (isset($method->data[0]->field) || in_array($item->name, ['Available types'])) {
                self::typesBuild($method, $interface);
            } elseif (isset($method->data[0]->parameter) || in_array($item->name, ['Getting updates', 'Available methods'])) {
                /* Telegram method */
                self::methodsBuild($method, $interface);
            } else {
                self::typesBuild($method, $interface);
            }
        }
    }

    /**
     * @param PhpNamespace $namespace
     * @param $item
     * @param array $comments
     * @return string
     */
    private static function interfaceBuild(PhpNamespace $namespace, $item, array $comments = []): string
    {
        $name = Helpers::className($item->name) . PhpPaths::Interface->name;

        $interface = new InterfaceType($name);
        $interface->addComment($item->name . PHP_EOL);
        $interface->addComment(Helpers::wordwrap($item->description) . PHP_EOL);

        if ($comments) {
            foreach ($comments as $key => $value) {
                $interface->addComment('@'. $key .' '. $value);
            }

            $interface->addComment('');
        }

        $namespace->add($interface);

        Helpers::save($namespace, $name);

        return $interface->getName();
    }

    /**
     * @return void
     */
    private static function abstracts(): void
    {
        $namespace = Helpers::namespace();
        $namespace->add(new ClassType('BaseMethod'));

        Helpers::save($namespace, 'BaseMethod');
        $namespace->removeClass('BaseMethod');

        $namespace->add(new ClassType('BaseType'));
        Helpers::save($namespace, 'BaseType');
    }

    /**
     * @param object $type
     * @param string $interface
     * @return void
     */
    private static function typesBuild(object $type, string $interface): void
    {
        if (str_contains($type->name, ' ')) return;

        $interface = Helpers::pathFromBaseNamespace(PhpPaths::Interface->name .'/'. $interface);

        $namespace = Helpers::namespace(PhpPaths::Types->name);
        $namespace->addUse(Helpers::pathFromBaseNamespace('BaseType'));
        $namespace->addUse($interface);

        $class = new ClassType(ucfirst($type->name));

        $class->addImplement($interface);

        $class->addComment(Helpers::wordwrap($type->description));

        $propertyBuilder = new Property;
        $properties = [];
        foreach ($type->data as $item) {
            /* Add property */
            $properties[] = $propertyBuilder->handle(
                $item->field,
                $item->type,
                $item->description);
        }

        $class->setProperties($properties);

        $class->setExtends(Helpers::pathFromBaseNamespace('BaseType'));

        $namespace->add($class);
        Helpers::save($namespace, $class->getName());
    }

    /**
     * @param object $method
     * @param string $interface
     * @return void
     */
    private static function methodsBuild(object $method, string $interface): void
    {
        if (str_contains($method->name, ' ')) return;

        $namespace = Helpers::namespace(PhpPaths::Methods->name);

        $class = new ClassType(ucfirst($method->name));
        $class->setComment(Helpers::wordwrap($method->description));

        $namespace->addUse(Helpers::pathFromBaseNamespace('BaseMethod'));
        $class->setExtends(Helpers::pathFromBaseNamespace('BaseMethod'));

        $interface = Helpers::pathFromBaseNamespace(PhpPaths::Interface->name .'/'. $interface);
        $namespace->addUse($interface);
        $class->addImplement($interface);

        $propertyBuilder = new Property;
        $properties = [];
        foreach ($method->data as $item) {
            /* Add property */
            $property = $propertyBuilder->handle(
                $item->parameter,
                $item->type,
                $item->description,
                $item->required !== 'Optional'
            );

            // add use
            foreach ($property->getType(true)->getTypes() as $type) {
                if (str_contains($type, $_ENV['BASE_NAMESPACE'])) {
                    $namespace->addUse($type);
                }
            }

            $properties[] = $property;
        }

        $class->setProperties($properties);

        $namespace->add($class);

        Helpers::save($namespace, ucfirst($method->name));
    }
}