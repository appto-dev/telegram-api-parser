<?php

namespace TelegramApiParser\CodeGenerator\Framework\Support;

class Str
{
    public static function toCamelCase(string $string, string $separator = ' '): string {
        return explode($separator, $string)
            |> (fn($x) => array_map('ucfirst', $x))
            |> (fn($x) => implode('', $x));
    }

    public static function linkedTypesExtractor(string $description) {
        preg_match_all('/\s*-\s*<a\s+href="[^"]*">([A-Z][A-Za-z0-9]*)<\/a>/', $description, $matches);

        return array_values(array_unique($matches[1]));
    }
}
