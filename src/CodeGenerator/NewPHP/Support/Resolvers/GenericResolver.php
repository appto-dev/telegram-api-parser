<?php

namespace Appto\TelegramBot\Support\Resolvers;

use InvalidArgumentException;
use Spatie\LaravelData\Data;

class GenericResolver
{
    public function __construct(
        protected array $map,
        protected string $discriminatorField = 'type',
    ) {}

    public function resolve($payload): mixed
    {
        if (!is_array($payload)) {
            if ($payload instanceof Data) {
                $payload = $payload->toArray();
            } else {
                throw new InvalidArgumentException('Expected array or Data object.');
            }
        }

        $type = $payload[$this->discriminatorField] ?? null;

        if (! $type || ! isset($this->map[$type])) {
            throw new InvalidArgumentException(
                "Unsupported discriminator value: {$type}"
            );
        }

        $class = $this->map[$type];

        return $class::from($payload);
    }

    public function resolveCollection(array $items): array
    {
        return array_map(fn ($item) => $this->resolve($item), $items);
    }
}
