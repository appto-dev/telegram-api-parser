<?php

namespace TelegramApiParser\CodeGenerator\NewPHP;

class Types
{
    /**
     * Приводит типы к PHP:
     *  - Integer -> int
     *  - Double -> float
     *
     * @param  string  $type
     * @return string
     */
    public static function normalizeType(string $type): string
    {
        return match (strtolower($type)) {
            'string'  => 'string',
            'true'    => 'true',
            'integer' => 'int',
            'boolean' => 'bool',
            'double'  => 'float',
            'float'   => 'float',
            'int'     => 'int',
            default   => $type
        };
    }

    public static function convertToBuiltinType(string $type, string $namespace): string
    {
        if (str_contains($type, '|')) {
            $types = array_map(function($item) use ($namespace) {
                if (self::isBuiltinType($item)) {
                    return $item;
                }

                if (str_contains($item, '[]')) {
                    $item = str_replace('[]', '', $item);
                    if (self::isBuiltinType($item)) {
                        return 'array';
                    }
                }

                return $namespace .'\\'. $item;
            }, explode('|', $type));

            return implode('|', $types);
        }

        $_temp = str_replace('[]', '', $type);
        if (self::isBuiltinType($_temp)) {
            return str_contains($type, '[]') ? 'array' : $type;
        }

        return $namespace . '\\'. $_temp;
    }

    /**
     * Проверит, является ли тип встроенным в PHP
     *
     * @param  string  $type
     * @return bool
     */
    public static function isBuiltinType(string $type): bool
    {
        return in_array(self::normalizeType($type), [
            'int', 'float', 'string', 'bool',
            'array', 'object', 'callable',
            'iterable', 'mixed', 'void',
            'null', 'false', 'true',
        ], true);
    }
}
