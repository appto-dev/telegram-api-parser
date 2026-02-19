<?php

namespace TelegramApiParser\CodeGenerator\NewPHP;

class ParserDocumentation
{
    const EXCLUDE_GROUP_NAMES = [
        'Recent changes', 'Authorizing your bot', 'Making requests',
        'Using a Local Bot API Server'
    ];

    const INTERFACE_NAMES = [
        'method' => 'TelegramBotDto',
        'type' => 'TelegramBotData'
    ];

    private array $interfaces = [
        'ReplyMarkup' => [
            'comment' =>
                'This object represents a reply markup that can be attached to a message. It can be one of:' . PHP_EOL .
                ' - <a href="#inlinekeyboardmarkup">InlineKeyboardMarkup</a> — an inline keyboard that appears next to the message and contains buttons of type <a href="#inlinekeyboardbutton">InlineKeyboardButton</a>' . PHP_EOL .
                ' - <a href="#replykeyboardmarkup">ReplyKeyboardMarkup</a> — a custom reply keyboard with button rows of type <a href="#keyboardbutton">KeyboardButton</a>.' . PHP_EOL .
                ' - <a href="#replykeyboardremove">ReplyKeyboardRemove</a> — removes the current custom reply keyboard and displays the default letter-keyboard.' . PHP_EOL .
                ' - <a href="#forcereply">ForceReply</a> — forces the user to reply to a specific message by displaying the reply interface.',
            'items' => [
                'InlineKeyboardMarkup',
                'ReplyKeyboardMarkup',
                'ReplyKeyboardRemove',
                'ForceReply'
            ]
        ]
    ];

    private array $bot_method_types = [];

    public function handle(array $documentation): array
    {
        $classes = [];

        // собираем все интерфейсы
        foreach ($documentation as $group) {
            if (in_array($group['name'], self::EXCLUDE_GROUP_NAMES, true)) {
                continue;
            }

            foreach ($group['sections'] as $section) {
                if (str_contains($section['name'], ' ')) continue;

                // объединит типы в интерфейсы
                if (! isset($section['parameters'])) {
                    if (! isset($section['return']) && (
                            strpos(strtolower($section['description']), "be one of") !== false ||
                            strpos(strtolower($section['description']), "- <a href=") !== false
                        )) {
                        $this->addInterface($section['name'], $section['description']);
                    }
                }
            }
        }

        foreach ($documentation as $group) {
            if (in_array($group['name'], self::EXCLUDE_GROUP_NAMES, true)) continue;

            foreach ($group['sections'] as $section) {
                if (str_contains($section['name'], ' ') || isset($this->getInterfaces()[$section['name']])) continue;

                $class = [
                    'name' => $section['name'],
                    'comment' => $section['description'],
                    'types' => [],
                    'interfaces' => array_unique(array_filter([
                        isset($section['return']) ? self::INTERFACE_NAMES['method'] : self::INTERFACE_NAMES['type'],
                        ...$this->useInterface($section['name'])
                    ])),
                    'properties' => [],
                ];

                if ($class['name'] == 'InputMedia') {
                    dd($class);
                }

                if (isset($section['parameters'])) {
                    // обработка параметров класса
                    foreach ($section['parameters'] as $parameter) {
                        if ($parameter['type']) {
                            $property = [
                                'name' => $parameter['name'],
                                'type' => $this->normalizeType($parameter['type'], $class['types']),
                                'comment' => $parameter['description'],
                            ];
                        }

                        if (isset($parameter['required'])) {
                            $property['required'] = $parameter['required'];
                        }

                        $class['properties'][] = $property;
                    }
                }

                sort($class['types']);
                $class['types'] = array_unique($class['types']);

                if (isset($section['return'])) {
                    $class['return'] = $this->normalizeType($section['return'], $class['types']);
                }

                foreach ($class['types'] as $index => $type) {
                    if ($type === $class['name']) {
                        unset($class['types'][$index]);
                    }
                }

                $class['types'] = array_values($class['types']);

                $classes[] = $class;
            }
        }

        return $classes;
    }

    public function getInterfaces(): array
    {
        return $this->interfaces;
    }

    public function getBotMethodTypes(): array
    {
        return array_values(array_unique($this->bot_method_types));
    }

    private function normalizeType(string|array $type, &$uses = []): string
    {
        // integer, string, Message etc.
        if (is_string($type) && !str_contains($type, 'or')) {
            $type = Types::normalizeType($type);

            if (! Types::isBuiltinType($type)) {
                $uses[] = $type;
            }

            return $type;
        }

        // integer or string
        if (is_string($type) && str_contains($type, ' or ')) {
            $user_types = [];
            $types = [];

            foreach (explode(' or ', $type) as $item) {
                $item = Types::normalizeType($item);
                $types[] = $item;

                if (! Types::isBuiltinType($item)) {
                    $user_types[] = $item;
                }
            }

            if ($user_types === $types) {
                // InlineKeyboardMarkup or ReplyKeyboardMarkup or ReplyKeyboardRemove or ForceReply == ReplyMarkup
                foreach ($this->interfaces as $interface => $data) {
                    if ($this->arraysEqual($data['items'], $types)) {
                        $uses[] = $interface;
                        return $interface;
                    }
                }
            } else {
                $uses = array_merge($uses, $user_types);
            }

            return implode('|', $types);
        }

        if (is_array($type) && count($type) === 1) {
            if (is_string($type[0])) {
                $temp_type = Types::normalizeType($type[0]);

                if (! Types::isBuiltinType($temp_type)) {
                    $uses[] = $temp_type;
                }

                return $temp_type.'[]';
            } elseif (is_array($type[0])) {
                $temp_type = Types::normalizeType($type[0][0]);

                if (! Types::isBuiltinType($temp_type)) {
                    $uses[] = $temp_type;
                }

                return $temp_type.'[][]';
            }
        }

        if (is_array($type)) {
            // InlineKeyboardMarkup or ReplyKeyboardMarkup or ReplyKeyboardRemove or ForceReply == ReplyMarkup
            foreach ($this->interfaces as $interface => $data) {
                if ($this->arraysEqual($data['items'], $type)) {
                    $uses[] = $interface;
                    return $interface;
                }
            }

            return implode('|', $type);
        }

        return $type;
    }

    /**
     * Добавит интерфейс и типы, которые относятся к нему
     *
     * @param  string  $name
     * @param  string  $description
     * @return void
     */
    private function addInterface(string $name, string $description): void
    {
        $rows = array_filter(explode("\n", $description), function (string $line) {
            return strpos($line, ' - ') !== false;
        });

        $this->interfaces[$name] = [
            'comment' => $description,
            'items' => array_values(
                array_map(function ($row) {
                    return substr(trim(strip_tags($row)), 2);
                }, $rows)
            )
        ];
    }

    private function arraysEqual(array $a, array $b): bool
    {
        sort($a);
        sort($b);

        return $a === $b;
    }

    private function useInterface(string $name): ?array {
        $interfaces = [];

        foreach ($this->interfaces as $interface => $data) {
            if (in_array($name, $data['items'])) {
                $interfaces[] = $interface;
            }
        }

        return $interfaces ?? null;
    }

    public function class_basename($class_name): string
    {
        return basename(str_replace('\\', '/', $class_name));
    }
}
