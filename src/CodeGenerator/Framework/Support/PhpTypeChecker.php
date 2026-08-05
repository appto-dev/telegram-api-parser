<?php

namespace TelegramApiParser\CodeGenerator\Framework\Support;

final class PhpTypeChecker
{
    private const NATIVE_TYPES = [
        'int', 'string', 'bool', 'float', 'array', 'object', 'mixed',
        'void', 'null', 'never', 'callable', 'iterable',
        'self', 'static', 'parent', 'true', 'false',
    ];

    /**
     * true - тип целиком состоит из нативных php-типов (в т.ч. union/nullable/массив).
     * false - хотя бы одна часть union-а является классом.
     */
    public function isNativeType(string $type): bool
    {
        $type = ltrim($type, '?');

        foreach (explode('|', $type) as $part) {
            $part = rtrim(trim($part), '[]'); // string[] -> string, PhotoSize[][] -> PhotoSize

            if ($part === '' || !in_array(strtolower($part), self::NATIVE_TYPES, true)) {
                return false;
            }
        }

        return true;
    }

    /** Все class-компоненты типа, если это не чисто нативный тип. */
    public function extractClassNames(string $type): array
    {
        $type = ltrim($type, '?');
        $classes = [];

        foreach (explode('|', $type) as $part) {
            $part = ltrim(rtrim(trim($part), '[]'), '\\');

            if ($part !== '' && !in_array(strtolower($part), self::NATIVE_TYPES, true)) {
                $classes[] = $part;
            }
        }

        return array_values(array_unique($classes));
    }
}
