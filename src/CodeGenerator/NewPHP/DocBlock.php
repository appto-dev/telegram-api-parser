<?php

namespace TelegramApiParser\CodeGenerator\NewPHP;

use ReflectionClass;

class DocBlock
{
    static public function make(array $properties): ?string
    {
        $return = [];

        foreach ($properties as $property) {
            $is_nullable = !$property['required'] ? '?' : null;
            $return[] = sprintf('%s%s: %s', $property['name'], $is_nullable, self::extractType($property['type']));
        }

        return $return
            ? sprintf('array{%s}', implode(', ', $return))
            : null;
    }

    private static function extractType(mixed $type)
    {
        if (Types::isBuiltinType($type)) {
            return $type;
        }

        if (str_contains($type, '[]')) {
            $count = substr_count($type, '[]');
            $clear_type = str_replace('[]', '', $type);

            if (! Types::isBuiltinType($clear_type)) {
                $telegram_type = Generator::$namespaces['types'].'\\'.$clear_type;

                if (class_exists($telegram_type)) {
                    $extract_class = self::extractByClass($telegram_type);
                    return $type .'|array<'. $extract_class.'>';
                }

                return $clear_type;
            }

            $result = $clear_type;

            for ($i = 0; $i < $count; $i++) {
                $result = 'array<'. $result .'>';
            }

            return $result;
        }

        if (str_contains($type, '|')) {
            $types = explode('|', $type);

            foreach ($types as $index => $item) {
                if (Types::isBuiltinType($item)) {
                    continue;
                }

                $telegram_type = Generator::$namespaces['types'].'\\'.$item;

                if (class_exists($telegram_type)) {
                    $types[$index] = $item .'|'. self::extractByClass($telegram_type);
                }

            }

            return implode('|', $types);
        }

        $telegram_type = Generator::$namespaces['types'].'\\'.$type;
        if (class_exists($telegram_type)) {
            return $type.'|'.self::extractByClass($telegram_type);
        }

        // interface
        return $type.'|array';
    }

    /**
     * @throws \ReflectionException
     */
    private static function extractByClass(string $telegram_type): ?string
    {
        $reflection = new ReflectionClass($telegram_type);

        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            if ($property->getModifiers() !== 1) continue;

            $types = $property->getType() instanceof \ReflectionUnionType
                ? $property->getType()->getTypes()
                : [ $property->getType() ];

            /** @var \ReflectionNamedType $type */
            foreach ($types as $index => $type) {
                if ($type->isBuiltin()) {
                    $types[$index] = $type->getName();
                    continue;
                }

                $types[$index] = array_last(explode('\\', $type->getName()));
            }

            $properties[] = [
                'name' => $property->getName(),
                'comment' => $property->getDocComment()
                    ? preg_replace('/\s{2,}/', ' ',
                        trim(
                            str_replace(PHP_EOL, '',
                                str_replace('* ', '', trim(substr($property->getDocComment(), 3, -2)))
                            )
                        )
                    )
                    : '',
                'type' => implode('|', $types),
                'required' => !$property->getType()->allowsNull(),
            ];
        }

        return self::make($properties) ?? 'array';
    }

    public static function getRecurseTypes(mixed $properties): array
    {
        $types = [];

        foreach ($properties as $property) {
            if (Types::isBuiltinType($property['type']) || Types::isBuiltinType(str_replace('[]', '', $property['type']))) {
                continue;
            }

            if (str_contains($property['type'], '|')) {
                foreach (explode('|', $property['type']) as $item) {
                    $item = str_replace('[]', '', $item);

                    if (Types::isBuiltinType($item)) continue;

                    $class_item = Generator::$namespaces['types'].'\\'.$item;
                    $interface_item = Generator::$namespaces['interfaces'].'\\'.$item;

                    if (class_exists($class_item) || interface_exists($interface_item)) {
                        $types[] = $class_item;
                    }
                }
            }

            $item = str_replace('[]', '', $property['type']);
            $class_item = Generator::$namespaces['types'].'\\'.$item;
            $interface_item = Generator::$namespaces['interfaces'].'\\'.$item;

            if (class_exists($class_item) || interface_exists($interface_item)) {
                $types[] = $class_item;
            }
        }

        if ($types) {
            $sub_types = [];

            foreach ($types as $class) {
                if (class_exists($class)) {
                    self::getRecurseTypesByClass($class, $sub_types);
                }
            }

            return array_unique(array_merge($types, $sub_types));
        }

        return $types;
    }

    private static function getRecurseTypesByClass(string $class, array &$sub_types) {
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getProperties() as $property) {
            $types = $property->getType() instanceof \ReflectionUnionType
                ? $property->getType()->getTypes()
                : [ $property->getType() ];

            foreach ($types as $type) {
                if ($type->isBuiltin()) continue;

                $sub_types[] = $type->getName();

                self::getRecurseTypesByClass($type->getName(), $sub_types);
            }
        }
    }

}
