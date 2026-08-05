<?php

namespace TelegramApiParser\CodeGenerator\Framework\Support;

final class TelegramTypeResolver
{
    private const array SCALAR_MAP = [
        'Integer' => 'int',
        'String' => 'string',
        'Boolean' => 'bool',
        'Float' => 'float',
        'Double' => 'float',
        'True' => 'true',
        'False' => 'false',
    ];

    public function __construct(private string $namespace) {}

    public function toPhpType(string|array $type): string
    {
        if (is_array($type)) {
            return 'array'; // элементы массива нативно не типизируются в любом случае
        }

        return $this->resolve($type, fqcn: true);
    }

    public function toDocType(string|array $type): string
    {
        if (is_array($type)) {
            return $this->resolveArrayDocType($type);
        }

        return $this->resolve($type, fqcn: false);
    }

    private function resolveArrayDocType(array $type): string
    {
        // ["InputMediaAudio"] - обычный "Array of X"
        if (count($type) === 1) {
            $inner = $type[0];

            return (is_array($inner) ? $this->resolveArrayDocType($inner) : $this->resolve($inner, fqcn: false)) . '[]';
        }

        // незарегистрированный union элементов - нативный докблок-union в скобках
        $variants = array_unique(array_map(
            fn (string|array $t) => is_array($t) ? $this->resolveArrayDocType($t) : $this->resolve($t, fqcn: false),
            $type
        ));

        return '(' . implode('|', $variants) . ')[]';
    }

    private function resolve(string $type, bool $fqcn): string
    {
        if (str_contains($type, ' or ')) {
            $variants = array_map(
                fn (string $part) => $this->resolve(trim($part), $fqcn),
                explode(' or ', $type)
            );

            return implode('|', array_unique($variants));
        }

        if (isset(self::SCALAR_MAP[$type])) {
            return self::SCALAR_MAP[$type];
        }

        return $fqcn ? $this->namespace . '\\' . $type : $type;
    }
}
