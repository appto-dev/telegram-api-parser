<?php

namespace TelegramApiParser\CodeGenerator\Framework\Support;

class Str
{
    public static function toCamelCase(string $string, string $separator = ' '): string {
        return implode('', array_map('ucfirst', explode($separator, $string)));
    }

    public static function linkedTypesExtractor(string $description): array {
        preg_match_all('/-\s*<a\s+href="[^"]*">([A-Z][A-Za-z0-9]*)<\/a>/', $description, $matches);

        return array_values(array_unique($matches[1]));
    }
}
